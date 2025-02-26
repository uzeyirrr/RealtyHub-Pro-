<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-listings')->only(['index', 'show']);
        $this->middleware('permission:create-listings')->only(['create', 'store']);
        $this->middleware('permission:edit-listings')->only(['edit', 'update']);
        $this->middleware('permission:delete-listings')->only('destroy');
    }

    /**
     * İlan listesi
     */
    public function index(Request $request)
    {
        $query = Property::with('agent')->latest();

        // Filtreler
        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sıralama
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'views':
                $query->orderByRaw('CAST(stats->>"$.view_count" AS UNSIGNED) DESC');
                break;
            default:
                $query->latest();
                break;
        }

        $properties = $query->paginate(12)->withQueryString();

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only(['type', 'status', 'sort']),
        ]);
    }

    /**
     * Yeni ilan formu
     */
    public function create()
    {
        $cities = config('locations.cities');
        $districts = config('locations.districts');

        return Inertia::render('Properties/Form', [
            'cities' => $cities,
            'districts' => $districts,
        ]);
    }

    /**
     * Yeni ilan kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'location' => 'required|array',
            'location.city' => 'required|string',
            'location.district' => 'required|string',
            'location.address' => 'required|string',
            'details' => 'required|array',
            'details.gross_sqm' => 'required|numeric|min:0',
            'details.net_sqm' => 'required|numeric|min:0',
            'details.rooms' => 'required|string',
            'details.floor' => 'required|numeric',
            'details.age' => 'required|numeric|min:0',
            'details.heating' => 'required|string',
            'media' => 'required|array',
            'media.images' => 'required|array|min:1',
            'media.images.*.file' => 'required|image|max:2048',
        ]);

        // Resimleri kaydet
        $images = [];
        foreach ($validated['media']['images'] as $image) {
            $path = $image['file']->store('properties', 'public');
            $images[] = [
                'url' => Storage::url($path),
                'path' => $path,
            ];
        }

        $property = Property::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'details' => $validated['details'],
            'media' => ['images' => $images],
            'stats' => [
                'view_count' => 0,
                'favorite_count' => 0,
            ],
            'agent_id' => auth()->id(),
        ]);

        return redirect()->route('properties.show', $property)
            ->with('success', 'İlan başarıyla oluşturuldu.');
    }

    /**
     * İlan detayı
     */
    public function show(Property $property)
    {
        $property->load('agent');
        $property->incrementViewCount();

        return Inertia::render('Properties/Show', [
            'property' => $property,
        ]);
    }

    /**
     * İlan düzenleme formu
     */
    public function edit(Property $property)
    {
        $property->load('agent');

        $cities = config('locations.cities');
        $districts = config('locations.districts');

        return Inertia::render('Properties/Form', [
            'property' => $property,
            'cities' => $cities,
            'districts' => $districts,
        ]);
    }

    /**
     * İlan güncelleme
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'location' => 'required|array',
            'location.city' => 'required|string',
            'location.district' => 'required|string',
            'location.address' => 'required|string',
            'details' => 'required|array',
            'details.gross_sqm' => 'required|numeric|min:0',
            'details.net_sqm' => 'required|numeric|min:0',
            'details.rooms' => 'required|string',
            'details.floor' => 'required|numeric',
            'details.age' => 'required|numeric|min:0',
            'details.heating' => 'required|string',
            'media' => 'required|array',
            'media.images' => 'required|array|min:1',
        ]);

        // Yeni resimler varsa kaydet
        $images = [];
        $existingImages = collect($property->media['images'] ?? []);

        foreach ($validated['media']['images'] as $image) {
            if (isset($image['file'])) {
                $path = $image['file']->store('properties', 'public');
                $images[] = [
                    'url' => Storage::url($path),
                    'path' => $path,
                ];
            } else {
                $images[] = $existingImages->firstWhere('url', $image['url']);
            }
        }

        // Eski resimleri sil
        $oldImages = $existingImages->pluck('path')->diff(collect($images)->pluck('path'));
        foreach ($oldImages as $path) {
            Storage::disk('public')->delete($path);
        }

        $property->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'details' => $validated['details'],
            'media' => ['images' => $images],
        ]);

        return redirect()->route('properties.show', $property)
            ->with('success', 'İlan başarıyla güncellendi.');
    }

    /**
     * İlan silme
     */
    public function destroy(Property $property)
    {
        // Resimleri sil
        if (isset($property->media['images'])) {
            foreach ($property->media['images'] as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }

        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'İlan başarıyla silindi.');
    }
} 