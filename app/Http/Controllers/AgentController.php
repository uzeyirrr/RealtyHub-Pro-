<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use App\Models\RealEstateOffice;
use App\Http\Resources\AgentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-agents')->only(['index', 'show']);
        $this->middleware('permission:create-agents')->only(['create', 'store']);
        $this->middleware('permission:edit-agents')->only(['edit', 'update']);
        $this->middleware('permission:delete-agents')->only('destroy');
    }

    /**
     * Danışman listesi
     */
    public function index(Request $request)
    {
        $agents = Agent::query()
            ->with(['user', 'office'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('license_number', 'like', "%{$search}%");
            })
            ->when($request->office_id, function ($query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->when($request->sort, function ($query, $sort) {
                [$column, $direction] = explode(',', $sort);
                $query->orderBy($column, $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate(10)
            ->withQueryString();

        $offices = RealEstateOffice::select('id', 'name')->get();

        return Inertia::render('Agents/Index', [
            'agents' => AgentResource::collection($agents),
            'offices' => $offices,
            'filters' => $request->only(['search', 'office_id', 'status', 'sort'])
        ]);
    }

    /**
     * Yeni danışman oluşturma formu
     */
    public function create()
    {
        $offices = RealEstateOffice::active()->get();
        $specializations = config('real-estate.specializations');
        $languages = config('real-estate.languages');

        return Inertia::render('Agents/Form', [
            'offices' => $offices,
            'specializations' => $specializations,
            'languages' => $languages
        ]);
    }

    /**
     * Yeni danışman kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'office_id' => 'required|exists:real_estate_offices,id',
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'license_number' => 'nullable|string|max:255|unique:agents',
            'license_date' => 'nullable|date',
            'specializations' => 'nullable|array',
            'languages' => 'nullable|array',
            'about' => 'nullable|string',
            'social_media' => 'nullable|array',
            'is_manager' => 'boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'working_areas' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            // Kullanıcı oluştur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            // Danışman rolü ata
            $user->assignRole('agent');

            // Fotoğraf yükle
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('agents', 'public');
            }

            // Danışman oluştur
            $agent = Agent::create([
                'user_id' => $user->id,
                'office_id' => $validated['office_id'],
                'title' => $validated['title'],
                'phone' => $validated['phone'],
                'photo' => $photoPath,
                'license_number' => $validated['license_number'],
                'license_date' => $validated['license_date'],
                'specializations' => $validated['specializations'],
                'languages' => $validated['languages'],
                'about' => $validated['about'],
                'social_media' => $validated['social_media'],
                'is_manager' => $validated['is_manager'],
                'commission_rate' => $validated['commission_rate'],
                'working_areas' => $validated['working_areas']
            ]);

            DB::commit();

            return redirect()->route('agents.index')
                ->with('success', 'Danışman başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->with('error', 'Danışman oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Danışman detayı
     */
    public function show(Agent $agent)
    {
        $agent->load(['user', 'office', 'properties' => function ($query) {
            $query->active()->latest()->limit(6);
        }]);

        return Inertia::render('Agents/Show', [
            'agent' => new AgentResource($agent)
        ]);
    }

    /**
     * Danışman düzenleme formu
     */
    public function edit(Agent $agent)
    {
        $agent->load(['user', 'office']);
        $offices = RealEstateOffice::active()->get();
        $specializations = config('real-estate.specializations');
        $languages = config('real-estate.languages');

        return Inertia::render('Agents/Form', [
            'agent' => new AgentResource($agent),
            'offices' => $offices,
            'specializations' => $specializations,
            'languages' => $languages
        ]);
    }

    /**
     * Danışman güncelleme
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $agent->user_id,
            'password' => 'nullable|string|min:8',
            'office_id' => 'required|exists:real_estate_offices,id',
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'license_number' => 'nullable|string|max:255|unique:agents,license_number,' . $agent->id,
            'license_date' => 'nullable|date',
            'specializations' => 'nullable|array',
            'languages' => 'nullable|array',
            'about' => 'nullable|string',
            'social_media' => 'nullable|array',
            'is_manager' => 'boolean',
            'is_active' => 'boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'working_areas' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            // Kullanıcı güncelle
            $agent->user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            if ($validated['password']) {
                $agent->user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            // Fotoğraf güncelle
            if ($request->hasFile('photo')) {
                if ($agent->photo) {
                    Storage::disk('public')->delete($agent->photo);
                }
                $validated['photo'] = $request->file('photo')->store('agents', 'public');
            }

            // Danışman güncelle
            $agent->update([
                'office_id' => $validated['office_id'],
                'title' => $validated['title'],
                'phone' => $validated['phone'],
                'photo' => $validated['photo'] ?? $agent->photo,
                'license_number' => $validated['license_number'],
                'license_date' => $validated['license_date'],
                'specializations' => $validated['specializations'],
                'languages' => $validated['languages'],
                'about' => $validated['about'],
                'social_media' => $validated['social_media'],
                'is_manager' => $validated['is_manager'],
                'is_active' => $validated['is_active'],
                'commission_rate' => $validated['commission_rate'],
                'working_areas' => $validated['working_areas']
            ]);

            DB::commit();

            return redirect()->route('agents.index')
                ->with('success', 'Danışman başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($validated['photo'])) {
                Storage::disk('public')->delete($validated['photo']);
            }

            return back()->with('error', 'Danışman güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Danışman silme
     */
    public function destroy(Agent $agent)
    {
        try {
            DB::beginTransaction();

            // Fotoğrafı sil
            if ($agent->photo) {
                Storage::disk('public')->delete($agent->photo);
            }

            // Danışmanı sil
            $agent->delete();
            
            // Kullanıcıyı sil
            $agent->user->delete();

            DB::commit();

            return redirect()->route('agents.index')
                ->with('success', 'Danışman başarıyla silindi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Danışman silinirken bir hata oluştu.');
        }
    }
} 