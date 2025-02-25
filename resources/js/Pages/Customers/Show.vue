<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Müşteri Detayı
        </h2>
        <div class="flex space-x-4">
          <Link
            :href="route('customers.edit', customer.id)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
          >
            Düzenle
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Temel Bilgiler -->
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Ad Soyad</p>
                <p class="mt-1">{{ customer.user.name }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">E-posta</p>
                <p class="mt-1">{{ customer.user.email }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Telefon</p>
                <p class="mt-1">{{ customer.phone }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Alternatif Telefon</p>
                <p class="mt-1">{{ customer.alternate_phone || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Adres Bilgileri -->
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adres Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500">Adres</p>
                <p class="mt-1">{{ customer.address || '-' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Şehir</p>
                <p class="mt-1">{{ customer.city || '-' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">İlçe</p>
                <p class="mt-1">{{ customer.district || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Müşteri Tipi ve Kurumsal Bilgiler -->
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Müşteri Tipi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Müşteri Tipi</p>
                <p class="mt-1">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': customer.customer_type === 'individual',
                      'bg-blue-100 text-blue-800': customer.customer_type === 'corporate'
                    }"
                  >
                    {{ customer.customer_type === 'individual' ? 'Bireysel' : 'Kurumsal' }}
                  </span>
                </p>
              </div>
            </div>

            <div v-if="customer.customer_type === 'corporate'" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Vergi Numarası</p>
                <p class="mt-1">{{ customer.tax_number }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Vergi Dairesi</p>
                <p class="mt-1">{{ customer.tax_office }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Firma Adı</p>
                <p class="mt-1">{{ customer.company_name }}</p>
              </div>
            </div>
          </div>

          <!-- Lead Bilgileri -->
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Lead Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Lead Kaynağı</p>
                <p class="mt-1">{{ customer.lead_source || '-' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Lead Durumu</p>
                <p class="mt-1">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800': customer.lead_status === 'new',
                      'bg-blue-100 text-blue-800': customer.lead_status === 'contacted',
                      'bg-green-100 text-green-800': customer.lead_status === 'qualified',
                      'bg-red-100 text-red-800': customer.lead_status === 'lost'
                    }"
                  >
                    {{ leadStatusText(customer.lead_status) }}
                  </span>
                </p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Sonraki Takip Tarihi</p>
                <p class="mt-1">{{ customer.next_followup ? formatDate(customer.next_followup) : '-' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Son İletişim</p>
                <p class="mt-1">{{ customer.last_contact ? formatDate(customer.last_contact) : '-' }}</p>
              </div>
              <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500">Notlar</p>
                <p class="mt-1 whitespace-pre-line">{{ customer.notes || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Tercihler -->
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tercihler</h3>
            <div class="grid grid-cols-1 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Müşteri Tercihleri</p>
                <p class="mt-1 whitespace-pre-line">{{ customer.preferences || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- İlgilenilen İlanlar -->
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">İlgilenilen İlanlar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Görüntülenen İlanlar</p>
                <div v-if="customer.viewed_properties?.length" class="space-y-2">
                  <div v-for="property in customer.viewed_properties" :key="property.id" class="flex items-center">
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      {{ property.title }}
                    </Link>
                    <span class="text-xs text-gray-500 ml-2">
                      ({{ formatDate(property.viewed_at) }})
                    </span>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">Henüz görüntülenen ilan yok</p>
              </div>

              <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Favori İlanlar</p>
                <div v-if="customer.favorite_properties?.length" class="space-y-2">
                  <div v-for="property in customer.favorite_properties" :key="property.id" class="flex items-center">
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      {{ property.title }}
                    </Link>
                  </div>
                </div>
                <p v-else class="text-sm text-gray-500">Henüz favori ilan yok</p>
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
import { format } from 'date-fns';
import { tr } from 'date-fns/locale';

const props = defineProps({
  customer: Object
});

const leadStatusText = (status) => {
  const statuses = {
    new: 'Yeni',
    contacted: 'İletişime Geçildi',
    qualified: 'Nitelikli',
    lost: 'Kaybedildi'
  };
  return statuses[status] || status;
};

const formatDate = (date) => {
  return format(new Date(date), 'dd MMM yyyy HH:mm', { locale: tr });
};
</script> 