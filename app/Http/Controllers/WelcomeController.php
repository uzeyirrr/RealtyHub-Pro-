<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        // Öne çıkan ilanları getir
        $featuredProperties = PropertyResource::collection(
            Property::with(['agent', 'office'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take(6)
                ->get()
        );

        // Şehirleri getir
        $cities = Property::where('is_active', true)
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        return Inertia::render('Welcome', [
            'featuredProperties' => $featuredProperties,
            'cities' => $cities,
        ]);
    }
} 