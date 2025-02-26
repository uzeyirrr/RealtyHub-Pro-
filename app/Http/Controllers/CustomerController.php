<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Agent;
use App\Models\RealEstateOffice;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-customers')->only(['index', 'show']);
        $this->middleware('permission:create-customers')->only(['create', 'store']);
        $this->middleware('permission:edit-customers')->only(['edit', 'update']);
        $this->middleware('permission:delete-customers')->only('destroy');
    }

    /**
     * Müşteri listesi
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
                ->orWhere('tax_number', 'like', "%{$search}%")
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
            ->when($request->sort, function ($query, $sort) {
                [$column, $direction] = explode(',', $sort);
                $query->orderBy($column, $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate(10)
            ->withQueryString();

        $agents = Agent::active()->with('user')->get();
        $offices = RealEstateOffice::active()->get();

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'agents' => $agents,
            'offices' => $offices,
            'filters' => $request->only(['search', 'agent_id', 'office_id', 'customer_type', 'lead_status', 'sort']),
            'customerTypes' => config('real-estate.customer_types'),
            'leadStatuses' => config('real-estate.lead_statuses')
        ]);
    }

    /**
     * Yeni müşteri oluşturma formu
     */
    public function create()
    {
        $agents = Agent::active()->with('user')->get();
        $offices = RealEstateOffice::active()->get();

        return Inertia::render('Customers/Form', [
            'agents' => $agents,
            'offices' => $offices,
            'customerTypes' => config('real-estate.customer_types'),
            'leadSources' => config('real-estate.lead_sources'),
            'leadStatuses' => config('real-estate.lead_statuses')
        ]);
    }

    /**
     * Yeni müşteri kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'agent_id' => 'nullable|exists:agents,id',
            'office_id' => 'nullable|exists:real_estate_offices,id',
            'phone' => 'nullable|string|max:255',
            'alternate_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'customer_type' => 'required|string|in:individual,corporate',
            'tax_number' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'preferences' => 'nullable|array',
            'lead_source' => 'nullable|string|max:255',
            'lead_status' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'next_followup' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            // Kullanıcı oluştur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            // Müşteri rolü ata
            $user->assignRole('customer');

            // Müşteri oluştur
            $customer = Customer::create([
                'user_id' => $user->id,
                'agent_id' => $validated['agent_id'],
                'office_id' => $validated['office_id'],
                'phone' => $validated['phone'],
                'alternate_phone' => $validated['alternate_phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'customer_type' => $validated['customer_type'],
                'tax_number' => $validated['tax_number'],
                'tax_office' => $validated['tax_office'],
                'company_name' => $validated['company_name'],
                'preferences' => $validated['preferences'],
                'lead_source' => $validated['lead_source'],
                'lead_status' => $validated['lead_status'],
                'notes' => $validated['notes'],
                'next_followup' => $validated['next_followup']
            ]);

            DB::commit();

            return redirect()->route('customers.index')
                ->with('success', 'Müşteri başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Müşteri oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Müşteri detayı
     */
    public function show(Customer $customer)
    {
        $customer->load(['user', 'agent.user', 'office']);

        return Inertia::render('Customers/Show', [
            'customer' => new CustomerResource($customer)
        ]);
    }

    /**
     * Müşteri düzenleme formu
     */
    public function edit(Customer $customer)
    {
        $customer->load(['user', 'agent', 'office']);
        $agents = Agent::active()->with('user')->get();
        $offices = RealEstateOffice::active()->get();

        return Inertia::render('Customers/Form', [
            'customer' => new CustomerResource($customer),
            'agents' => $agents,
            'offices' => $offices,
            'customerTypes' => config('real-estate.customer_types'),
            'leadSources' => config('real-estate.lead_sources'),
            'leadStatuses' => config('real-estate.lead_statuses')
        ]);
    }

    /**
     * Müşteri güncelleme
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->user_id,
            'password' => 'nullable|string|min:8',
            'agent_id' => 'nullable|exists:agents,id',
            'office_id' => 'nullable|exists:real_estate_offices,id',
            'phone' => 'nullable|string|max:255',
            'alternate_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'customer_type' => 'required|string|in:individual,corporate',
            'tax_number' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'preferences' => 'nullable|array',
            'lead_source' => 'nullable|string|max:255',
            'lead_status' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'next_followup' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Kullanıcı güncelle
            $customer->user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            if ($validated['password']) {
                $customer->user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            // Müşteri güncelle
            $customer->update([
                'agent_id' => $validated['agent_id'],
                'office_id' => $validated['office_id'],
                'phone' => $validated['phone'],
                'alternate_phone' => $validated['alternate_phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'customer_type' => $validated['customer_type'],
                'tax_number' => $validated['tax_number'],
                'tax_office' => $validated['tax_office'],
                'company_name' => $validated['company_name'],
                'preferences' => $validated['preferences'],
                'lead_source' => $validated['lead_source'],
                'lead_status' => $validated['lead_status'],
                'notes' => $validated['notes'],
                'next_followup' => $validated['next_followup'],
                'is_active' => $validated['is_active']
            ]);

            // Son iletişim tarihini güncelle
            $customer->updateLastContact();

            DB::commit();

            return redirect()->route('customers.index')
                ->with('success', 'Müşteri başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Müşteri güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Müşteri silme
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();

            // Müşteriyi sil
            $customer->delete();
            
            // Kullanıcıyı sil
            $customer->user->delete();

            DB::commit();

            return redirect()->route('customers.index')
                ->with('success', 'Müşteri başarıyla silindi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Müşteri silinirken bir hata oluştu.');
        }
    }

    /**
     * Görüntülenen ilanları kaydet
     */
    public function addViewedProperty(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $customer->addToViewedProperties($validated['property_id']);

        return back()->with('success', 'İlan görüntüleme kaydedildi.');
    }

    /**
     * Favori ilanları yönet
     */
    public function toggleFavoriteProperty(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        if ($customer->hasFavoriteProperty($validated['property_id'])) {
            $customer->removeFromFavorites($validated['property_id']);
            $message = 'İlan favorilerden kaldırıldı.';
        } else {
            $customer->addToFavorites($validated['property_id']);
            $message = 'İlan favorilere eklendi.';
        }

        return back()->with('success', $message);
    }
} 