<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Customer;
use App\Models\Agent;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Controller constructor'ı
     */
    protected function middleware()
    {
        return ['auth', 'verified'];
    }

    /**
     * Dashboard ana sayfası
     */
    public function index()
    {
        // İstatistikleri getir
        $stats = [
            'active_properties_count' => Property::where('is_active', true)->count(),
            'active_customers_count' => Customer::where('is_active', true)->count(),
            'agents_count' => Agent::where('is_active', true)->count(),
            'monthly_sales_total' => Property::where('status_text', 'sold')
                ->whereMonth('updated_at', now()->month)
                ->sum('price'),
        ];

        // Son eklenen ilanları getir
        $latestProperties = PropertyResource::collection(
            Property::with(['agent', 'office'])
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get()
        );

        // Son müşteri aktivitelerini getir
        $latestActivities = DB::table('customer_property_views as cpv')
            ->join('customers as c', 'c.id', '=', 'cpv.customer_id')
            ->join('properties as p', 'p.id', '=', 'cpv.property_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->select([
                'cpv.id',
                'u.name as customer_name',
                DB::raw("CONCAT(p.title, ' ilanını görüntüledi') as action"),
                'cpv.created_at'
            ])
            ->union(
                DB::table('customer_property_favorites as cpf')
                    ->join('customers as c', 'c.id', '=', 'cpf.customer_id')
                    ->join('properties as p', 'p.id', '=', 'cpf.property_id')
                    ->join('users as u', 'u.id', '=', 'c.user_id')
                    ->select([
                        'cpf.id',
                        'u.name as customer_name',
                        DB::raw("CONCAT(p.title, ' ilanını favorilere ekledi') as action"),
                        'cpf.created_at'
                    ])
            )
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'latestProperties' => $latestProperties,
            'latestActivities' => $latestActivities,
        ]);
    }
} 