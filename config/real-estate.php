<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Müşteri Tipleri
    |--------------------------------------------------------------------------
    */
    'customer_types' => [
        'individual' => 'Bireysel',
        'corporate' => 'Kurumsal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Durumları
    |--------------------------------------------------------------------------
    */
    'lead_statuses' => [
        'new' => 'Yeni',
        'contacted' => 'İletişime Geçildi',
        'meeting_scheduled' => 'Görüşme Planlandı',
        'qualified' => 'Nitelikli',
        'proposal_sent' => 'Teklif Gönderildi',
        'negotiating' => 'Görüşme Aşamasında',
        'won' => 'Kazanıldı',
        'lost' => 'Kaybedildi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Kaynakları
    |--------------------------------------------------------------------------
    */
    'lead_sources' => [
        'website' => 'Web Sitesi',
        'referral' => 'Referans',
        'social_media' => 'Sosyal Medya',
        'advertisement' => 'Reklam',
        'cold_call' => 'Soğuk Arama',
        'exhibition' => 'Fuar',
        'other' => 'Diğer',
    ],

    /*
    |--------------------------------------------------------------------------
    | İlan Tipleri
    |--------------------------------------------------------------------------
    */
    'property_types' => [
        'apartment' => 'Daire',
        'villa' => 'Villa',
        'land' => 'Arsa',
        'commercial' => 'İşyeri',
        'building' => 'Bina',
    ],

    /*
    |--------------------------------------------------------------------------
    | İlan Durumları
    |--------------------------------------------------------------------------
    */
    'property_statuses' => [
        'for_sale' => 'Satılık',
        'for_rent' => 'Kiralık',
        'sold' => 'Satıldı',
        'rented' => 'Kiralandı',
    ],

    /*
    |--------------------------------------------------------------------------
    | Danışman Uzmanlık Alanları
    |--------------------------------------------------------------------------
    */
    'specializations' => [
        'residential' => 'Konut',
        'commercial' => 'Ticari',
        'land' => 'Arsa',
        'luxury' => 'Lüks Konut',
        'investment' => 'Yatırım',
    ],

    /*
    |--------------------------------------------------------------------------
    | Diller
    |--------------------------------------------------------------------------
    */
    'languages' => [
        'tr' => 'Türkçe',
        'en' => 'İngilizce',
        'de' => 'Almanca',
        'ar' => 'Arapça',
        'ru' => 'Rusça',
    ],

    /*
    |--------------------------------------------------------------------------
    | Medya Ayarları
    |--------------------------------------------------------------------------
    */
    'media' => [
        'max_property_images' => 10,
        'max_image_size' => 2048, // KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'watermark' => [
            'enabled' => true,
            'path' => 'images/watermark.png',
            'position' => 'bottom-right',
            'opacity' => 50,
        ],
    ],
]; 