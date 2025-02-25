<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $properties = Property::query()
            ->with(['office', 'agent'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->office_id, function ($query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->when($request->agent_id, function ($query, $agentId) {
                $query->where('agent_id', $agentId);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->when($request->district, function ($query, $district) {
                $query->where('district', $district);
            })
            ->when($request->min_price, function ($query, $minPrice) {
                $query->where('price', '>=', $minPrice);
            })
            ->when($request->max_price, function ($query, $maxPrice) {
                $query->where('price', '<=', $maxPrice);
            })
            ->when($request->min_sqm, function ($query, $minSqm) {
                $query->where('gross_sqm', '>=', $minSqm);
            })
            ->when($request->max_sqm, function ($query, $maxSqm) {
                $query->where('gross_sqm', '<=', $maxSqm);
            })
            ->when($request->rooms, function ($query, $rooms) {
                $query->where('rooms', $rooms);
            })
            ->when($request->is_furnished !== null, function ($query) use ($request) {
                $query->where('is_furnished', $request->boolean('is_furnished'));
            })
            ->when($request->features, function ($query, $features) {
                foreach ($features as $feature) {
                    $query->whereJsonContains('features', $feature);
                }
            })
            ->when($request->sort_by, function ($query, $sortBy) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                if (in_array($sortBy, ['price', 'created_at', 'view_count'])) {
                    $query->orderBy($sortBy, $direction);
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($request->is_featured !== null, function ($query) use ($request) {
                $query->where('is_featured', $request->boolean('is_featured'));
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->paginate($request->per_page ?? 15);

        return PropertyResource::collection($properties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:real_estate_offices,id',
            'agent_id' => 'required|exists:agents,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'rooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floor' => 'nullable|integer',
            'total_floors' => 'nullable|integer|min:1',
            'age' => 'nullable|integer|min:0',
            'gross_sqm' => 'nullable|numeric|min:0',
            'net_sqm' => 'nullable|numeric|min:0',
            'heating_type' => 'nullable|string',
            'is_furnished' => 'boolean',
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'neighborhood' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'features' => 'nullable|array',
            'media' => 'nullable|array',
            'media.*' => 'image|max:5120', // Her bir resim max 5MB
            'video_url' => 'nullable|url',
            'virtual_tour_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'commission_rate' => 'nullable|numeric|between:0,100',
            'seo' => 'nullable|array',
        ]);

        // Medya dosyalarını yükle
        if ($request->hasFile('media')) {
            $mediaFiles = [];
            foreach ($request->file('media') as $file) {
                $mediaFiles[] = $file->store('properties/media', 'public');
            }
            $validated['media'] = $mediaFiles;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status_text'] = 'available';

        $property = Property::create($validated);

        return new PropertyResource($property);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $property->incrementViewCount();
        return new PropertyResource($property->load(['office', 'agent']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'office_id' => 'sometimes|required|exists:real_estate_offices,id',
            'agent_id' => 'sometimes|required|exists:agents,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|size:3',
            'rooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floor' => 'nullable|integer',
            'total_floors' => 'nullable|integer|min:1',
            'age' => 'nullable|integer|min:0',
            'gross_sqm' => 'nullable|numeric|min:0',
            'net_sqm' => 'nullable|numeric|min:0',
            'heating_type' => 'nullable|string',
            'is_furnished' => 'boolean',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'district' => 'sometimes|required|string',
            'neighborhood' => 'sometimes|required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'features' => 'nullable|array',
            'media' => 'nullable|array',
            'media.*' => 'image|max:5120',
            'video_url' => 'nullable|url',
            'virtual_tour_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'status_text' => ['sometimes', 'required', Rule::in(['available', 'sold', 'rented'])],
            'commission_rate' => 'nullable|numeric|between:0,100',
            'seo' => 'nullable|array',
        ]);

        if ($request->hasFile('media')) {
            $mediaFiles = $property->media ?? [];
            foreach ($request->file('media') as $file) {
                $mediaFiles[] = $file->store('properties/media', 'public');
            }
            $validated['media'] = $mediaFiles;
        }

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $property->update($validated);

        return new PropertyResource($property);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return response()->json(['message' => 'İlan başarıyla silindi.']);
    }

    /**
     * Increment view count for the property.
     */
    public function incrementView(Property $property)
    {
        $property->incrementViewCount();
        return response()->json(['view_count' => $property->view_count]);
    }

    /**
     * Toggle favorite status for the property.
     */
    public function toggleFavorite(Property $property)
    {
        // Bu metod müşteri/kullanıcı sistemi entegre edildiğinde güncellenecek
        $property->incrementFavoriteCount();
        return response()->json(['favorite_count' => $property->favorite_count]);
    }

    /**
     * Get distinct property types.
     */
    public function types()
    {
        $types = Property::distinct()->pluck('type');
        return response()->json($types);
    }

    /**
     * Get distinct cities.
     */
    public function cities()
    {
        $cities = Property::distinct()->pluck('city');
        return response()->json($cities);
    }

    /**
     * Get distinct districts for a city.
     */
    public function districts(Request $request)
    {
        $districts = Property::query()
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->distinct()
            ->pluck('district');

        return response()->json($districts);
    }

    /**
     * Get distinct neighborhoods for a district.
     */
    public function neighborhoods(Request $request)
    {
        $neighborhoods = Property::query()
            ->when($request->district, function ($query, $district) {
                $query->where('district', $district);
            })
            ->distinct()
            ->pluck('neighborhood');

        return response()->json($neighborhoods);
    }

    /**
     * Get price statistics for properties.
     */
    public function priceStats(Request $request)
    {
        $query = Property::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->city) {
            $query->where('city', $request->city);
        }

        if ($request->district) {
            $query->where('district', $request->district);
        }

        $stats = [
            'min_price' => $query->min('price'),
            'max_price' => $query->max('price'),
            'avg_price' => $query->avg('price'),
            'avg_price_per_sqm' => $query->whereNotNull('gross_sqm')
                ->where('gross_sqm', '>', 0)
                ->selectRaw('AVG(price / gross_sqm) as avg_price_per_sqm')
                ->value('avg_price_per_sqm')
        ];

        return response()->json($stats);
    }
}
