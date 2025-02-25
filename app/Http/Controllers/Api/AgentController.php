<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Http\Resources\AgentResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $agents = Agent::query()
            ->with(['user', 'office'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('license_number', 'like', "%{$search}%");
            })
            ->when($request->office_id, function ($query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->when($request->specializations, function ($query, $specializations) {
                $query->whereJsonContains('specializations', $specializations);
            })
            ->when($request->languages, function ($query, $languages) {
                $query->whereJsonContains('languages', $languages);
            })
            ->when($request->is_manager !== null, function ($query) use ($request) {
                $query->where('is_manager', $request->boolean('is_manager'));
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->paginate($request->per_page ?? 15);

        return AgentResource::collection($agents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'office_id' => 'required|exists:real_estate_offices,id',
            'title' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'license_number' => 'nullable|string|unique:agents',
            'license_date' => 'nullable|date',
            'specializations' => 'nullable|array',
            'languages' => 'nullable|array',
            'about' => 'nullable|string',
            'social_media' => 'nullable|array',
            'is_manager' => 'boolean',
            'commission_rate' => 'nullable|numeric|between:0,100',
            'working_areas' => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('agents/photos', 'public');
        }

        $agent = Agent::create($validated);

        return new AgentResource($agent);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        return new AgentResource($agent->load(['user', 'office']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'office_id' => 'sometimes|required|exists:real_estate_offices,id',
            'title' => 'nullable|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'license_number' => ['nullable', 'string', Rule::unique('agents')->ignore($agent->id)],
            'license_date' => 'nullable|date',
            'specializations' => 'nullable|array',
            'languages' => 'nullable|array',
            'about' => 'nullable|string',
            'social_media' => 'nullable|array',
            'is_manager' => 'boolean',
            'is_active' => 'boolean',
            'commission_rate' => 'nullable|numeric|between:0,100',
            'working_areas' => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('agents/photos', 'public');
        }

        $agent->update($validated);

        return new AgentResource($agent);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();

        return response()->json(['message' => 'Emlak danışmanı başarıyla silindi.']);
    }

    /**
     * Get all specializations.
     */
    public function specializations()
    {
        $specializations = Agent::distinct()
            ->pluck('specializations')
            ->flatten()
            ->unique()
            ->values();

        return response()->json($specializations);
    }

    /**
     * Get all languages.
     */
    public function languages()
    {
        $languages = Agent::distinct()
            ->pluck('languages')
            ->flatten()
            ->unique()
            ->values();

        return response()->json($languages);
    }
}
