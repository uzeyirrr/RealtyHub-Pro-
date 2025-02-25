<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RealEstateOffice;
use App\Http\Resources\RealEstateOfficeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RealEstateOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offices = RealEstateOffice::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            })
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->when($request->district, function ($query, $district) {
                $query->where('district', $district);
            })
            ->when($request->is_franchise !== null, function ($query) use ($request) {
                $query->where('is_franchise', $request->boolean('is_franchise'));
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->paginate($request->per_page ?? 15);

        return RealEstateOfficeResource::collection($offices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:real_estate_offices',
            'tax_number' => 'required|string|unique:real_estate_offices',
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'website' => 'nullable|url',
            'is_franchise' => 'boolean',
            'parent_office_id' => [
                'nullable',
                Rule::exists('real_estate_offices', 'id')->where(function ($query) {
                    $query->whereNull('parent_office_id');
                }),
            ],
            'working_hours' => 'nullable|array',
            'social_media' => 'nullable|array',
            'about' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('offices/logos', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $office = RealEstateOffice::create($validated);

        return new RealEstateOfficeResource($office);
    }

    /**
     * Display the specified resource.
     */
    public function show(RealEstateOffice $office)
    {
        return new RealEstateOfficeResource($office);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RealEstateOffice $office)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => ['sometimes', 'required', 'email', Rule::unique('real_estate_offices')->ignore($office->id)],
            'tax_number' => ['sometimes', 'required', 'string', Rule::unique('real_estate_offices')->ignore($office->id)],
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'district' => 'sometimes|required|string',
            'website' => 'nullable|url',
            'is_franchise' => 'boolean',
            'parent_office_id' => [
                'nullable',
                Rule::exists('real_estate_offices', 'id')->where(function ($query) use ($office) {
                    $query->whereNull('parent_office_id')
                        ->where('id', '!=', $office->id);
                }),
            ],
            'working_hours' => 'nullable|array',
            'social_media' => 'nullable|array',
            'about' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('offices/logos', 'public');
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $office->update($validated);

        return new RealEstateOfficeResource($office);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RealEstateOffice $office)
    {
        $office->delete();

        return response()->json(['message' => 'Emlak ofisi başarıyla silindi.']);
    }

    public function cities()
    {
        $cities = RealEstateOffice::distinct('city')->pluck('city');
        return response()->json($cities);
    }

    public function districts(Request $request)
    {
        $districts = RealEstateOffice::query()
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->distinct('district')
            ->pluck('district');

        return response()->json($districts);
    }
}
