<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Rolleri oluştur
        $roles = [
            [
                'name' => 'Yönetici',
                'slug' => 'admin',
                'description' => 'Sistem yöneticisi',
            ],
            [
                'name' => 'Takım Lideri',
                'slug' => 'team_lead',
                'description' => 'Emlak danışmanları takım lideri',
            ],
            [
                'name' => 'Kıdemli Danışman',
                'slug' => 'senior_agent',
                'description' => 'Kıdemli emlak danışmanı',
            ],
            [
                'name' => 'Danışman',
                'slug' => 'agent',
                'description' => 'Emlak danışmanı',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // İzinleri oluştur
        $permissions = [
            // İlan yönetimi
            [
                'name' => 'İlan Görüntüleme',
                'slug' => 'view-listings',
                'description' => 'İlanları görüntüleyebilir',
                'module' => 'listings',
            ],
            [
                'name' => 'İlan Oluşturma',
                'slug' => 'create-listings',
                'description' => 'Yeni ilan oluşturabilir',
                'module' => 'listings',
            ],
            [
                'name' => 'İlan Düzenleme',
                'slug' => 'edit-listings',
                'description' => 'İlanları düzenleyebilir',
                'module' => 'listings',
            ],
            [
                'name' => 'İlan Silme',
                'slug' => 'delete-listings',
                'description' => 'İlanları silebilir',
                'module' => 'listings',
            ],

            // Danışman yönetimi
            [
                'name' => 'Danışman Görüntüleme',
                'slug' => 'view-agents',
                'description' => 'Danışmanları görüntüleyebilir',
                'module' => 'agents',
            ],
            [
                'name' => 'Danışman Oluşturma',
                'slug' => 'create-agents',
                'description' => 'Yeni danışman ekleyebilir',
                'module' => 'agents',
            ],
            [
                'name' => 'Danışman Düzenleme',
                'slug' => 'edit-agents',
                'description' => 'Danışmanları düzenleyebilir',
                'module' => 'agents',
            ],
            [
                'name' => 'Danışman Silme',
                'slug' => 'delete-agents',
                'description' => 'Danışmanları silebilir',
                'module' => 'agents',
            ],

            // Müşteri yönetimi
            [
                'name' => 'Müşteri Görüntüleme',
                'slug' => 'view-customers',
                'description' => 'Müşterileri görüntüleyebilir',
                'module' => 'customers',
            ],
            [
                'name' => 'Müşteri Oluşturma',
                'slug' => 'create-customers',
                'description' => 'Yeni müşteri ekleyebilir',
                'module' => 'customers',
            ],
            [
                'name' => 'Müşteri Düzenleme',
                'slug' => 'edit-customers',
                'description' => 'Müşterileri düzenleyebilir',
                'module' => 'customers',
            ],
            [
                'name' => 'Müşteri Silme',
                'slug' => 'delete-customers',
                'description' => 'Müşterileri silebilir',
                'module' => 'customers',
            ],

            // Satış yönetimi
            [
                'name' => 'Satış Görüntüleme',
                'slug' => 'view-sales',
                'description' => 'Satışları görüntüleyebilir',
                'module' => 'sales',
            ],
            [
                'name' => 'Satış Oluşturma',
                'slug' => 'create-sales',
                'description' => 'Yeni satış ekleyebilir',
                'module' => 'sales',
            ],
            [
                'name' => 'Satış Düzenleme',
                'slug' => 'edit-sales',
                'description' => 'Satışları düzenleyebilir',
                'module' => 'sales',
            ],
            [
                'name' => 'Satış Silme',
                'slug' => 'delete-sales',
                'description' => 'Satışları silebilir',
                'module' => 'sales',
            ],

            // Raporlar
            [
                'name' => 'Rapor Görüntüleme',
                'slug' => 'view-reports',
                'description' => 'Raporları görüntüleyebilir',
                'module' => 'reports',
            ],
            [
                'name' => 'Rapor Oluşturma',
                'slug' => 'create-reports',
                'description' => 'Yeni rapor oluşturabilir',
                'module' => 'reports',
            ],

            // Ayarlar
            [
                'name' => 'Ayarları Görüntüleme',
                'slug' => 'view-settings',
                'description' => 'Sistem ayarlarını görüntüleyebilir',
                'module' => 'settings',
            ],
            [
                'name' => 'Ayarları Düzenleme',
                'slug' => 'edit-settings',
                'description' => 'Sistem ayarlarını düzenleyebilir',
                'module' => 'settings',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Rollere izinleri ata
        $admin = Role::where('slug', 'admin')->first();
        $teamLead = Role::where('slug', 'team_lead')->first();
        $seniorAgent = Role::where('slug', 'senior_agent')->first();
        $agent = Role::where('slug', 'agent')->first();

        // Yönetici tüm izinlere sahip
        $admin->permissions()->attach(Permission::all());

        // Takım lideri izinleri
        $teamLeadPermissions = [
            'view-listings', 'create-listings', 'edit-listings',
            'view-agents', 'create-agents', 'edit-agents',
            'view-customers', 'create-customers', 'edit-customers',
            'view-sales', 'create-sales', 'edit-sales',
            'view-reports', 'create-reports',
            'view-settings',
        ];
        $teamLead->permissions()->attach(
            Permission::whereIn('slug', $teamLeadPermissions)->get()
        );

        // Kıdemli danışman izinleri
        $seniorAgentPermissions = [
            'view-listings', 'create-listings', 'edit-listings',
            'view-agents',
            'view-customers', 'create-customers', 'edit-customers',
            'view-sales', 'create-sales',
            'view-reports',
        ];
        $seniorAgent->permissions()->attach(
            Permission::whereIn('slug', $seniorAgentPermissions)->get()
        );

        // Danışman izinleri
        $agentPermissions = [
            'view-listings', 'create-listings',
            'view-customers', 'create-customers',
            'view-sales', 'create-sales',
        ];
        $agent->permissions()->attach(
            Permission::whereIn('slug', $agentPermissions)->get()
        );

        // Varsayılan admin kullanıcısı oluştur
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@yezuriemlak.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $adminUser->assignRole('admin');
    }
} 