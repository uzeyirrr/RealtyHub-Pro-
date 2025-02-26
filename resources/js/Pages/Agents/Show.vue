<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Danışman Detayı
        </h2>
        <div class="flex space-x-4">
          <Link
            v-if="$page.props.auth.user.permissions.includes('edit-agents')"
            :href="route('agents.edit', agent.id)"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700"
          >
            Düzenle
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Üst Bilgi -->
            <div class="flex flex-col md:flex-row md:space-x-8">
              <!-- Profil Fotoğrafı -->
              <div class="flex-shrink-0 mb-6 md:mb-0">
                <img
                  :src="agent.photo_url"
                  :alt="agent.user.name"
                  class="h-48 w-48 rounded-lg object-cover"
                />
              </div>

              <!-- Temel Bilgiler -->
              <div class="flex-grow">
                <h3 class="text-2xl font-bold text-gray-900">
                  {{ agent.user.name }}
                </h3>
                <p class="text-lg text-gray-600">{{ agent.title }}</p>
                <p class="text-gray-500">{{ agent.office.name }}</p>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-500">Telefon</p>
                    <p class="font-medium">{{ agent.phone }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">E-posta</p>
                    <p class="font-medium">{{ agent.user.email }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Lisans No</p>
                    <p class="font-medium">{{ agent.license_number || '-' }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Lisans Tarihi</p>
                    <p class="font-medium">{{ agent.license_date || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- İstatistikler -->
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Aktif İlanlar</p>
                <p class="text-2xl font-bold text-primary-600">
                  {{ agent.stats.active_listings_count }}
                </p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Aktif Müşteriler</p>
                <p class="text-2xl font-bold text-primary-600">
                  {{ agent.stats.active_customers_count }}
                </p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Toplam Satış</p>
                <p class="text-2xl font-bold text-primary-600">
                  {{ agent.stats.total_sales }}
                </p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Toplam Kiralama</p>
                <p class="text-2xl font-bold text-primary-600">
                  {{ agent.stats.total_rentals }}
                </p>
              </div>
            </div>

            <!-- Detay Bilgileri -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Sol Kolon -->
              <div>
                <h4 class="text-lg font-semibold mb-4">Uzmanlık Alanları</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="specialization in agent.specializations"
                    :key="specialization"
                    class="px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-sm"
                  >
                    {{ specialization }}
                  </span>
                </div>

                <h4 class="text-lg font-semibold mt-6 mb-4">Çalışma Bölgeleri</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="area in agent.working_areas"
                    :key="area"
                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"
                  >
                    {{ area }}
                  </span>
                </div>
              </div>

              <!-- Sağ Kolon -->
              <div>
                <h4 class="text-lg font-semibold mb-4">Yabancı Diller</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="language in agent.languages"
                    :key="language"
                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"
                  >
                    {{ language }}
                  </span>
                </div>

                <h4 class="text-lg font-semibold mt-6 mb-4">Hakkında</h4>
                <p class="text-gray-600">{{ agent.about || 'Henüz bir açıklama eklenmemiş.' }}</p>
              </div>
            </div>

            <!-- Aktif İlanlar -->
            <div class="mt-8">
              <h4 class="text-lg font-semibold mb-4">Aktif İlanlar</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="property in agent.properties"
                  :key="property.id"
                  class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
                >
                  <img
                    :src="property.media[0]"
                    :alt="property.title"
                    class="h-48 w-full object-cover"
                  />
                  <div class="p-4">
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-lg font-semibold text-gray-900 hover:text-primary-600"
                    >
                      {{ property.title }}
                    </Link>
                    <p class="text-primary-600 font-bold mt-2">
                      {{ property.formatted_price }}
                    </p>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                      <MapPinIcon class="h-4 w-4 mr-1" />
                      {{ property.location.district }}, {{ property.location.city }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { MapPinIcon } from '@heroicons/vue/24/outline';

defineProps({
  agent: {
    type: Object,
    required: true
  }
});
</script> 