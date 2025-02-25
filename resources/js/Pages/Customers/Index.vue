<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Müşteriler
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtreler -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Arama</label>
              <input
                type="text"
                v-model="filters.search"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="İsim, e-posta veya telefon"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Müşteri Tipi</label>
              <select
                v-model="filters.customer_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Tümü</option>
                <option value="individual">Bireysel</option>
                <option value="corporate">Kurumsal</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Lead Durumu</label>
              <select
                v-model="filters.lead_status"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Tümü</option>
                <option value="new">Yeni</option>
                <option value="contacted">İletişime Geçildi</option>
                <option value="qualified">Nitelikli</option>
                <option value="lost">Kaybedildi</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Müşteri Listesi -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">Müşteri Listesi</h3>
              <Link
                :href="route('customers.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
              >
                Yeni Müşteri Ekle
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Müşteri
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      İletişim
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tip
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Lead Durumu
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Son İletişim
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      İşlemler
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="customer in customers.data" :key="customer.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            {{ customer.user.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ customer.user.email }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ customer.phone }}</div>
                      <div class="text-sm text-gray-500">{{ customer.city }}, {{ customer.district }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': customer.customer_type === 'individual',
                          'bg-blue-100 text-blue-800': customer.customer_type === 'corporate'
                        }"
                      >
                        {{ customer.customer_type === 'individual' ? 'Bireysel' : 'Kurumsal' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(customer.last_contact) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link
                        :href="route('customers.edit', customer.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                      >
                        Düzenle
                      </Link>
                      <button
                        @click="deleteCustomer(customer)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Sil
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Sayfalama -->
            <div class="mt-6">
              <Pagination :links="customers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';
import { tr } from 'date-fns/locale';

const props = defineProps({
  customers: Object,
  filters: Object
});

const filters = ref({
  search: props.filters?.search || '',
  customer_type: props.filters?.customer_type || '',
  lead_status: props.filters?.lead_status || ''
});

watch(filters, (newFilters) => {
  router.get(route('customers.index'), newFilters, {
    preserveState: true,
    preserveScroll: true
  });
}, { deep: true });

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

const deleteCustomer = (customer) => {
  if (confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')) {
    router.delete(route('customers.destroy', customer.id));
  }
};
</script> 