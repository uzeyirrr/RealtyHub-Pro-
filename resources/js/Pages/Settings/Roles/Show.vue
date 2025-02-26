<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Rol Detayı
        </h2>
        <Link
          :href="route('roles.edit', role.id)"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Düzenle
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Rol Bilgileri -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Rol Bilgileri</h3>
              <dl class="grid grid-cols-1 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Rol Adı</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ role.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Slug</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ role.slug }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Açıklama</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ role.description || '-' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Oluşturulma Tarihi</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(role.created_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- İzinler -->
          <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">İzinler</h3>
              <div class="space-y-6">
                <div v-for="(modulePermissions, module) in groupedPermissions" :key="module">
                  <h4 class="text-base font-medium text-gray-900 capitalize mb-3">{{ formatModuleName(module) }}</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="permission in modulePermissions" :key="permission.id" class="flex items-center">
                      <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span class="text-sm text-gray-900">{{ permission.name }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kullanıcılar -->
        <div class="mt-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Bu Role Sahip Kullanıcılar</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kullanıcı
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        E-posta
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Durum
                      </th>
                      <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">İşlemler</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in role.users" :key="user.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              v-if="user.photo"
                              :src="user.photo"
                              :alt="user.name"
                              class="h-10 w-10 rounded-full object-cover"
                            />
                            <div
                              v-else
                              class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center"
                            >
                              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ user.name }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ user.email }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          :class="[
                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                            user.status === 'active'
                              ? 'bg-green-100 text-green-800'
                              : 'bg-gray-100 text-gray-800'
                          ]"
                        >
                          {{ user.status === 'active' ? 'Aktif' : 'Pasif' }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Link
                          :href="route('users.edit', user.id)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Düzenle
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  role: Object,
});

const groupedPermissions = computed(() => {
  return props.role.permissions.reduce((acc, permission) => {
    if (!acc[permission.module]) {
      acc[permission.module] = [];
    }
    acc[permission.module].push(permission);
    return acc;
  }, {});
});

const formatModuleName = (module) => {
  const moduleNames = {
    listings: 'İlan Yönetimi',
    agents: 'Danışman Yönetimi',
    customers: 'Müşteri Yönetimi',
    sales: 'Satış Yönetimi',
    reports: 'Raporlar',
    settings: 'Ayarlar',
  };

  return moduleNames[module] || module;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};
</script> 