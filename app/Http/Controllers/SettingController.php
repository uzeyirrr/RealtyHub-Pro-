<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-settings')->only(['index']);
        $this->middleware('permission:edit-settings')->only(['update']);
    }

    /**
     * Ayarlar sayfasını göster
     */
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'settings' => $this->getSettings(),
            'roles' => $this->getRoles(),
            'permissions' => $this->getPermissions(),
        ]);
    }

    /**
     * Ayarları güncelle
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_phone' => 'required|string|max:20',
            'company_email' => 'required|email|max:255',
            'company_tax_number' => 'required|string|max:20',
            'company_tax_office' => 'required|string|max:255',
            'company_logo' => 'nullable|image|max:2048',
            'notification_settings' => 'required|array',
            'notification_settings.email' => 'required|boolean',
            'notification_settings.sms' => 'required|boolean',
            'notification_settings.push' => 'required|boolean',
            'notification_settings.whatsapp' => 'required|boolean',
            'currency_settings' => 'required|array',
            'currency_settings.default' => 'required|string|in:TRY,USD,EUR',
            'currency_settings.show_all' => 'required|boolean',
            'listing_settings' => 'required|array',
            'listing_settings.max_photos' => 'required|integer|min:1|max:50',
            'listing_settings.photo_quality' => 'required|integer|min:60|max:100',
            'listing_settings.watermark' => 'required|boolean',
            'listing_settings.auto_publish' => 'required|boolean',
            'map_settings' => 'required|array',
            'map_settings.provider' => 'required|string|in:google,openstreetmap',
            'map_settings.api_key' => 'required_if:map_settings.provider,google|string',
            'map_settings.default_lat' => 'required|numeric',
            'map_settings.default_lng' => 'required|numeric',
            'map_settings.default_zoom' => 'required|integer|min:1|max:20',
        ]);

        // Logo işleme
        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        // Ayarları güncelle
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        // Cache'i temizle
        Cache::forget('settings');

        return redirect()->back()->with('success', 'Ayarlar başarıyla güncellendi.');
    }

    /**
     * Logo yükleme işlemi
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'company-logo.' . $file->getClientOriginalExtension();
                
                // Eski logoyu sil
                $oldLogo = Setting::where('key', 'company_logo')->first();
                if ($oldLogo && Storage::exists('public/company/' . $oldLogo->value)) {
                    Storage::delete('public/company/' . $oldLogo->value);
                }
                
                // Yeni logoyu kaydet
                $path = $file->storeAs('public/company', $filename);
                
                Setting::set('company_logo', $filename);
                
                Cache::forget('settings.company_logo');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logo başarıyla yüklendi.',
                    'logo_url' => Storage::url($path)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Logo yüklenemedi.'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logo yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tüm ayarları getir
     */
    private function getSettings()
    {
        return Cache::remember('settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Rolleri getir
     */
    private function getRoles()
    {
        return [
            'admin' => 'Yönetici',
            'manager' => 'Müdür',
            'agent' => 'Danışman',
            'assistant' => 'Asistan',
        ];
    }

    /**
     * İzinleri getir
     */
    private function getPermissions()
    {
        return [
            'listings' => [
                'view-listings' => 'İlanları Görüntüleme',
                'create-listings' => 'İlan Oluşturma',
                'edit-listings' => 'İlan Düzenleme',
                'delete-listings' => 'İlan Silme',
            ],
            'agents' => [
                'view-agents' => 'Danışmanları Görüntüleme',
                'create-agents' => 'Danışman Oluşturma',
                'edit-agents' => 'Danışman Düzenleme',
                'delete-agents' => 'Danışman Silme',
            ],
            'customers' => [
                'view-customers' => 'Müşterileri Görüntüleme',
                'create-customers' => 'Müşteri Oluşturma',
                'edit-customers' => 'Müşteri Düzenleme',
                'delete-customers' => 'Müşteri Silme',
            ],
            'reports' => [
                'view-reports' => 'Raporları Görüntüleme',
                'export-reports' => 'Rapor Dışa Aktarma',
            ],
            'settings' => [
                'view-settings' => 'Ayarları Görüntüleme',
                'edit-settings' => 'Ayarları Düzenleme',
            ],
        ];
    }
} 