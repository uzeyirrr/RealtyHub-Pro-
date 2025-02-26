<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Customer;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-reports');
    }

    /**
     * Raporlama ana sayfası
     */
    public function index()
    {
        return Inertia::render('Reports/Index', [
            'summary' => $this->getSummaryStats(),
            'propertyStats' => $this->getPropertyStats(),
            'customerStats' => $this->getCustomerStats(),
            'agentStats' => $this->getAgentStats(),
        ]);
    }

    /**
     * Özet istatistikler
     */
    private function getSummaryStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return [
            'total_properties' => Property::count(),
            'active_properties' => Property::active()->count(),
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'total_agents' => Agent::count(),
            'active_agents' => Agent::active()->count(),
            'monthly_stats' => [
                'new_properties' => Property::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'new_customers' => Customer::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'properties_sold' => Property::where('status', 'sold')
                    ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                    ->count(),
                'properties_rented' => Property::where('status', 'rented')
                    ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                    ->count(),
            ]
        ];
    }

    /**
     * İlan istatistikleri
     */
    private function getPropertyStats()
    {
        return [
            'by_type' => Property::select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->get()
                ->pluck('total', 'type'),
            
            'by_status' => Property::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status'),
            
            'price_ranges' => [
                '0-500000' => Property::whereBetween('price', [0, 500000])->count(),
                '500001-1000000' => Property::whereBetween('price', [500001, 1000000])->count(),
                '1000001-2000000' => Property::whereBetween('price', [1000001, 2000000])->count(),
                '2000001+' => Property::where('price', '>', 2000000)->count(),
            ],
            
            'most_viewed' => Property::orderBy('view_count', 'desc')
                ->take(5)
                ->get(['id', 'title', 'type', 'price', 'view_count']),
            
            'most_favorited' => Property::withCount('favoriteCustomers')
                ->orderBy('favorite_customers_count', 'desc')
                ->take(5)
                ->get(['id', 'title', 'type', 'price'])
        ];
    }

    /**
     * Müşteri istatistikleri
     */
    private function getCustomerStats()
    {
        return [
            'by_type' => Customer::select('customer_type', DB::raw('count(*) as total'))
                ->groupBy('customer_type')
                ->get()
                ->pluck('total', 'customer_type'),
            
            'by_lead_status' => Customer::select('lead_status', DB::raw('count(*) as total'))
                ->groupBy('lead_status')
                ->get()
                ->pluck('total', 'lead_status'),
            
            'by_lead_source' => Customer::select('lead_source', DB::raw('count(*) as total'))
                ->groupBy('lead_source')
                ->get()
                ->pluck('total', 'lead_source'),
            
            'most_active' => Customer::withCount(['viewedProperties', 'favoriteProperties'])
                ->orderBy('viewed_properties_count', 'desc')
                ->take(5)
                ->get()
        ];
    }

    /**
     * Danışman istatistikleri
     */
    private function getAgentStats()
    {
        return [
            'top_performers' => Agent::withCount(['properties' => function ($query) {
                    $query->where('status', 'sold')
                        ->orWhere('status', 'rented');
                }])
                ->orderBy('properties_count', 'desc')
                ->take(5)
                ->get(),
            
            'most_active' => Agent::withCount(['properties', 'customers'])
                ->orderBy('properties_count', 'desc')
                ->take(5)
                ->get(),
            
            'by_specialization' => Agent::select('specialization', DB::raw('count(*) as total'))
                ->groupBy('specialization')
                ->get()
                ->pluck('total', 'specialization')
        ];
    }

    /**
     * Özel tarih aralığı raporu
     */
    public function customDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        return Inertia::render('Reports/CustomDateRange', [
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'stats' => [
                'new_properties' => Property::whereBetween('created_at', [$startDate, $endDate])->count(),
                'properties_sold' => Property::where('status', 'sold')
                    ->whereBetween('updated_at', [$startDate, $endDate])
                    ->count(),
                'properties_rented' => Property::where('status', 'rented')
                    ->whereBetween('updated_at', [$startDate, $endDate])
                    ->count(),
                'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
                'converted_leads' => Customer::where('lead_status', 'won')
                    ->whereBetween('updated_at', [$startDate, $endDate])
                    ->count(),
            ]
        ]);
    }

    /**
     * Excel raporu oluştur
     */
    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:properties,customers,agents',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Excel export işlemleri burada yapılacak
        // Laravel Excel paketi kullanılabilir
    }
}
