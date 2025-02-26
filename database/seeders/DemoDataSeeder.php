<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RealEstateOffice;
use App\Models\Agent;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rolleri oluştur
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Sistem yöneticisi'
        ]);

        $agentRole = Role::create([
            'name' => 'Emlak Danışmanı',
            'slug' => 'agent',
            'description' => 'Emlak danışmanı'
        ]);

        // İzinleri oluştur
        $permissions = [
            'view-dashboard' => 'Dashboard görüntüleme',
            'manage-offices' => 'Ofis yönetimi',
            'manage-agents' => 'Danışman yönetimi',
            'manage-properties' => 'İlan yönetimi',
            'manage-customers' => 'Müşteri yönetimi',
            'view-reports' => 'Raporları görüntüleme',
            'manage-settings' => 'Ayarları yönetme'
        ];

        foreach ($permissions as $slug => $name) {
            Permission::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $name
            ]);
        }

        // Admin rolüne tüm izinleri ver
        $adminRole->permissions()->attach(Permission::all());

        // Agent rolüne bazı izinleri ver
        $agentRole->permissions()->attach(
            Permission::whereIn('slug', [
                'view-dashboard',
                'manage-properties',
                'manage-customers',
                'view-reports'
            ])->get()
        );

        // Admin kullanıcısı oluştur
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $admin->roles()->attach($adminRole);

        // Emlak ofisi oluştur
        $office = RealEstateOffice::create([
            'name' => 'RealtyHub Merkez Ofis',
            'slug' => 'realtyhub-merkez-ofis',
            'phone' => '0212 123 45 67',
            'email' => 'info@realtyhub.com',
            'tax_number' => '1234567890',
            'address' => 'Bağdat Caddesi No:123',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'is_active' => true,
        ]);

        // Danışmanlar oluştur
        $agents = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Danışman {$i}",
                'email' => "agent{$i}@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $agents[] = Agent::create([
                'user_id' => $user->id,
                'office_id' => $office->id,
                'title' => 'Emlak Danışmanı',
                'phone' => "0532 123 45 6{$i}",
                'is_active' => true,
            ]);
        }

        // Müşteriler oluştur
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "Müşteri {$i}",
                'email' => "customer{$i}@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            Customer::create([
                'user_id' => $user->id,
                'agent_id' => $agents[array_rand($agents)]->id,
                'office_id' => $office->id,
                'phone' => "0542 123 45 6{$i}",
                'city' => 'İstanbul',
                'district' => ['Kadıköy', 'Beşiktaş', 'Şişli'][array_rand(['Kadıköy', 'Beşiktaş', 'Şişli'])],
                'is_active' => true,
            ]);
        }

        // İlan tipleri
        $propertyTypes = ['Daire', 'Villa', 'Müstakil Ev', 'Arsa', 'İş Yeri'];
        $districts = ['Kadıköy', 'Beşiktaş', 'Şişli', 'Üsküdar', 'Maltepe'];
        $neighborhoods = [
            'Kadıköy' => ['Fenerbahçe', 'Caddebostan', 'Suadiye', 'Erenköy'],
            'Beşiktaş' => ['Levent', 'Etiler', 'Bebek', 'Arnavutköy'],
            'Şişli' => ['Nişantaşı', 'Teşvikiye', 'Harbiye', 'Mecidiyeköy'],
            'Üsküdar' => ['Kuzguncuk', 'Beylerbeyi', 'Çengelköy', 'Acıbadem'],
            'Maltepe' => ['Bağlarbaşı', 'Cevizli', 'Küçükyalı', 'Altayçeşme'],
        ];

        // İlanlar oluştur
        for ($i = 1; $i <= 20; $i++) {
            $district = $districts[array_rand($districts)];
            $neighborhood = $neighborhoods[$district][array_rand($neighborhoods[$district])];
            $type = $propertyTypes[array_rand($propertyTypes)];
            $rooms = rand(1, 5);
            $bathrooms = rand(1, 3);
            $floor = rand(1, 10);
            $age = rand(0, 20);
            $grossSqm = rand(80, 300);
            $netSqm = round($grossSqm * 0.85);

            Property::create([
                'office_id' => $office->id,
                'agent_id' => $agents[array_rand($agents)]->id,
                'title' => "{$district} {$neighborhood}'da {$rooms}+1 {$type}",
                'slug' => Str::slug("{$district} {$neighborhood}'da {$rooms}+1 {$type}-{$i}"),
                'type' => $type,
                'status' => rand(0, 1) ? 'Satılık' : 'Kiralık',
                'price' => rand(500000, 5000000),
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                'address' => "{$neighborhood} Mahallesi, {$district}",
                'city' => 'İstanbul',
                'district' => $district,
                'neighborhood' => $neighborhood,
                'location' => [
                    'latitude' => 41.0082 + (rand(-1000, 1000) / 10000),
                    'longitude' => 28.9784 + (rand(-1000, 1000) / 10000),
                ],
                'details' => [
                    'rooms' => $rooms,
                    'bathrooms' => $bathrooms,
                    'gross_sqm' => $grossSqm,
                    'net_sqm' => $netSqm,
                    'floor' => $floor,
                    'total_floors' => $floor + rand(0, 5),
                    'age' => $age,
                    'heating_type' => 'Kombi',
                    'balcony' => (bool)rand(0, 1),
                    'furniture' => (bool)rand(0, 1),
                    'garden' => (bool)rand(0, 1),
                    'elevator' => (bool)rand(0, 1),
                    'parking' => (bool)rand(0, 1),
                ],
                'media' => [
                    'main_image' => 'images/properties/property-' . rand(1, 5) . '.jpg',
                    'images' => [
                        'images/properties/property-' . rand(1, 5) . '.jpg',
                        'images/properties/property-' . rand(1, 5) . '.jpg',
                        'images/properties/property-' . rand(1, 5) . '.jpg',
                    ],
                ],
                'is_active' => true,
                'is_featured' => rand(0, 1) ? true : false,
            ]);
        }
    }
}
