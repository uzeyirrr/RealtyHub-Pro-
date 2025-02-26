<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
        });

        // Varsayılan ayarları ekle
        $defaultSettings = [
            [
                'key' => 'company_name',
                'value' => 'Yezuri Emlak',
                'group' => 'company',
                'type' => 'string'
            ],
            [
                'key' => 'company_address',
                'value' => 'İstanbul, Türkiye',
                'group' => 'company',
                'type' => 'string'
            ],
            [
                'key' => 'company_phone',
                'value' => '+90 xxx xxx xx xx',
                'group' => 'company',
                'type' => 'string'
            ],
            [
                'key' => 'company_email',
                'value' => 'info@yezuriemlak.com',
                'group' => 'company',
                'type' => 'string'
            ],
            [
                'key' => 'notification_settings',
                'value' => json_encode([
                    'email' => true,
                    'sms' => false,
                    'push' => true,
                    'whatsapp' => false
                ]),
                'group' => 'notifications',
                'type' => 'json'
            ],
            [
                'key' => 'currency_settings',
                'value' => json_encode([
                    'default' => 'TRY',
                    'show_all' => true
                ]),
                'group' => 'currency',
                'type' => 'json'
            ],
            [
                'key' => 'listing_settings',
                'value' => json_encode([
                    'max_photos' => 10,
                    'photo_quality' => 80,
                    'watermark' => true,
                    'auto_publish' => false
                ]),
                'group' => 'listings',
                'type' => 'json'
            ],
            [
                'key' => 'map_settings',
                'value' => json_encode([
                    'provider' => 'google',
                    'api_key' => '',
                    'default_lat' => 41.0082,
                    'default_lng' => 28.9784,
                    'default_zoom' => 13
                ]),
                'group' => 'maps',
                'type' => 'json'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
