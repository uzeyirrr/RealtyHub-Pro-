<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Property;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->with(['user', 'agent', 'office'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('alternate_phone', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->when($request->agent_id, function ($query, $agentId) {
                $query->where('agent_id', $agentId);
            })
            ->when($request->office_id, function ($query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->when($request->customer_type, function ($query, $type) {
                $query->where('customer_type', $type);
            })
            ->when($request->lead_status, function ($query, $status) {
                $query->where('lead_status', $status);
            })
            ->when($request->lead_source, function ($query, $source) {
                $query->where('lead_source', $source);
            })
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->when($request->district, function ($query, $district) {
                $query->where('district', $district);
            })
            ->when($request->needs_followup, function ($query) {
                $query->where('next_followup', '<=', now());
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->when($request->sort_by, function ($query, $sortBy) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                if (in_array($sortBy, ['created_at', 'last_contact', 'next_followup'])) {
                    $query->orderBy($sortBy, $direction);
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($request->per_page ?? 15);

        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'agent_id' => 'nullable|exists:agents,id',
            'office_id' => 'nullable|exists:real_estate_offices,id',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'customer_type' => ['required', Rule::in(['individual', 'corporate'])],
            'tax_number' => 'nullable|string|unique:customers',
            'tax_office' => 'nullable|string',
            'company_name' => 'nullable|string',
            'preferences' => 'nullable|array',
            'lead_source' => 'nullable|string',
            'lead_status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'lost'])],
            'notes' => 'nullable|string',
            'next_followup' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        if ($validated['customer_type'] === 'corporate') {
            $request->validate([
                'tax_number' => 'required|string',
                'tax_office' => 'required|string',
                'company_name' => 'required|string',
            ]);
        }

        $validated['last_contact'] = now();
        $customer = Customer::create($validated);

        return new CustomerResource($customer);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer->load(['user', 'agent', 'office']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'agent_id' => 'nullable|exists:agents,id',
            'office_id' => 'nullable|exists:real_estate_offices,id',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'customer_type' => ['sometimes', 'required', Rule::in(['individual', 'corporate'])],
            'tax_number' => ['nullable', 'string', Rule::unique('customers')->ignore($customer->id)],
            'tax_office' => 'nullable|string',
            'company_name' => 'nullable|string',
            'preferences' => 'nullable|array',
            'lead_source' => 'nullable|string',
            'lead_status' => ['sometimes', 'required', Rule::in(['new', 'contacted', 'qualified', 'lost'])],
            'notes' => 'nullable|string',
            'next_followup' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['customer_type']) && $validated['customer_type'] === 'corporate') {
            $request->validate([
                'tax_number' => 'required|string',
                'tax_office' => 'required|string',
                'company_name' => 'required|string',
            ]);
        }

        $customer->update($validated);

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Müşteri başarıyla silindi.']);
    }

    /**
     * Add a property to customer's viewed properties list.
     */
    public function addViewedProperty(Request $request, Customer $customer)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $customer->addToViewedProperties($request->property_id);
        
        return response()->json(['message' => 'Görüntülenen mülk başarıyla eklendi.']);
    }

    /**
     * Toggle a property in customer's favorites list.
     */
    public function toggleFavoriteProperty(Request $request, Customer $customer)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $property = Property::findOrFail($request->property_id);
        $favorites = $customer->favorite_properties ?? [];

        if (in_array($request->property_id, $favorites)) {
            $customer->removeFromFavorites($request->property_id);
            $property->decrementFavoriteCount();
            $message = 'Mülk favorilerden kaldırıldı.';
        } else {
            $customer->addToFavorites($request->property_id);
            $property->incrementFavoriteCount();
            $message = 'Mülk favorilere eklendi.';
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Update customer's last contact date and add notes.
     */
    public function updateContact(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
            'next_followup' => 'nullable|date|after:now',
            'lead_status' => ['nullable', Rule::in(['new', 'contacted', 'qualified', 'lost'])],
        ]);

        $customer->updateLastContact();
        
        if (isset($validated['next_followup'])) {
            $customer->scheduleNextFollowup($validated['next_followup']);
        }

        if (isset($validated['lead_status'])) {
            $customer->update(['lead_status' => $validated['lead_status']]);
        }

        // Mevcut notlara yeni notu ekle
        $existingNotes = $customer->notes ? $customer->notes . "\n\n" : '';
        $newNote = now()->format('Y-m-d H:i') . " - " . $validated['notes'];
        $customer->update(['notes' => $existingNotes . $newNote]);

        return new CustomerResource($customer);
    }

    /**
     * Get customer statistics.
     */
    public function statistics(Request $request)
    {
        $query = Customer::query();

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        $stats = [
            'total_customers' => $query->count(),
            'active_customers' => $query->where('is_active', true)->count(),
            'individual_customers' => $query->where('customer_type', 'individual')->count(),
            'corporate_customers' => $query->where('customer_type', 'corporate')->count(),
            'lead_status_counts' => $query->selectRaw('lead_status, count(*) as count')
                ->groupBy('lead_status')
                ->pluck('count', 'lead_status'),
            'needs_followup' => $query->where('next_followup', '<=', now())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get distinct lead sources.
     */
    public function leadSources()
    {
        $sources = Customer::distinct()->pluck('lead_source');
        return response()->json($sources);
    }

    /**
     * Get customers by lead status.
     */
    public function byLeadStatus(Request $request)
    {
        $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'lost'])]
        ]);

        $customers = Customer::where('lead_status', $request->status)
            ->with(['user', 'agent'])
            ->paginate($request->per_page ?? 15);

        return CustomerResource::collection($customers);
    }
}
