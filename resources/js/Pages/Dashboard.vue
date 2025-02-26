<script setup>
import { onMounted, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Chart from 'chart.js/auto';
import { Link } from '@inertiajs/vue3';
import { 
  BuildingOfficeIcon, 
  UserGroupIcon, 
  UsersIcon, 
  CurrencyDollarIcon,
  UserCircleIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  stats: {
    type: Object,
    required: true,
    default: () => ({
      active_properties_count: 0,
      active_customers_count: 0,
      agents_count: 0,
      monthly_sales_total: 0
    })
  },
  latestProperties: {
    type: Object,
    required: true,
    default: () => ({
      data: []
    })
  },
  latestActivities: {
    type: Array,
    required: true,
    default: () => []
  }
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};
</script>

<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Aktif İlanlar -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                  <BuildingOfficeIcon class="h-6 w-6 text-primary-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Aktif İlanlar
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.active_properties_count }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Aktif Müşteriler -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                  <UserGroupIcon class="h-6 w-6 text-green-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Aktif Müşteriler
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.active_customers_count }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Danışmanlar -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                  <UsersIcon class="h-6 w-6 text-blue-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Danışmanlar
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ stats.agents_count }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Toplam Satış -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                  <CurrencyDollarIcon class="h-6 w-6 text-yellow-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Aylık Satış
                    </dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">
                        {{ formatPrice(stats.monthly_sales_total) }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Son Eklenen İlanlar -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                Son Eklenen İlanlar
              </h3>
              <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200">
                  <li v-for="property in latestProperties.data" :key="property.id" class="py-4">
                    <div class="flex items-center space-x-4">
                      <div class="flex-shrink-0">
                        <img class="h-12 w-12 rounded-md object-cover" 
                          :src="property.media?.main_image || '/images/property-placeholder.jpg'" 
                          :alt="property.title" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-900">
                          {{ property.title }}
                        </p>
                        <p class="truncate text-sm text-gray-500">
                          {{ property.location?.district }}/{{ property.location?.city }}
                        </p>
                      </div>
                      <div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-sm font-medium"
                          :class="{
                            'bg-green-100 text-green-800': property.status === 'Satılık',
                            'bg-blue-100 text-blue-800': property.status === 'Kiralık',
                          }">
                          {{ formatPrice(property.price) }}
                        </span>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="mt-6">
                <Link :href="route('properties.index')" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Tüm İlanları Görüntüle
                </Link>
              </div>
            </div>
          </div>

          <!-- Son Müşteri Aktiviteleri -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                Son Müşteri Aktiviteleri
              </h3>
              <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200">
                  <li v-for="activity in latestActivities" :key="activity.id" class="py-4">
                    <div class="flex items-center space-x-4">
                      <div class="flex-shrink-0">
                        <UserCircleIcon class="h-10 w-10 text-gray-400" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                          {{ activity.customer_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ activity.action }}
                        </p>
                      </div>
                      <div class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(activity.created_at) }}
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="mt-6">
                <Link :href="route('customers.index')" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Tüm Müşterileri Görüntüle
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
