<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ customer ? 'Müşteri Düzenle' : 'Yeni Müşteri Ekle' }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <!-- Temel Bilgiler -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                    <input
                      type="text"
                      v-model="form.name"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">E-posta</label>
                    <input
                      type="email"
                      v-model="form.email"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Telefon</label>
                    <input
                      type="tel"
                      v-model="form.phone"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">
                      {{ form.errors.phone }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Alternatif Telefon</label>
                    <input
                      type="tel"
                      v-model="form.alternate_phone"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>

              <!-- Adres Bilgileri -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Adres Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Adres</label>
                    <textarea
                      v-model="form.address"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Şehir</label>
                    <input
                      type="text"
                      v-model="form.city"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">İlçe</label>
                    <input
                      type="text"
                      v-model="form.district"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>

              <!-- Müşteri Tipi ve Kurumsal Bilgiler -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Müşteri Tipi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Müşteri Tipi</label>
                    <select
                      v-model="form.customer_type"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                      <option value="individual">Bireysel</option>
                      <option value="corporate">Kurumsal</option>
                    </select>
                  </div>
                </div>

                <div v-if="form.customer_type === 'corporate'" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Vergi Numarası</label>
                    <input
                      type="text"
                      v-model="form.tax_number"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Vergi Dairesi</label>
                    <input
                      type="text"
                      v-model="form.tax_office"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Firma Adı</label>
                    <input
                      type="text"
                      v-model="form.company_name"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                  </div>
                </div>
              </div>

              <!-- Lead Bilgileri -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Lead Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Lead Kaynağı</label>
                    <input
                      type="text"
                      v-model="form.lead_source"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Lead Durumu</label>
                    <select
                      v-model="form.lead_status"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                      <option value="new">Yeni</option>
                      <option value="contacted">İletişime Geçildi</option>
                      <option value="qualified">Nitelikli</option>
                      <option value="lost">Kaybedildi</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Sonraki Takip Tarihi</label>
                    <input
                      type="datetime-local"
                      v-model="form.next_followup"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Notlar</label>
                    <textarea
                      v-model="form.notes"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- Tercihler -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tercihler</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Müşteri Tercihleri</label>
                    <textarea
                      v-model="form.preferences"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      placeholder="Müşterinin özel tercihleri, ilgilendiği mülk tipleri, bütçe aralığı vb."
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- Butonlar -->
              <div class="flex justify-end space-x-4">
                <Link
                  :href="route('customers.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  İptal
                </Link>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                  :disabled="form.processing"
                >
                  {{ customer ? 'Güncelle' : 'Kaydet' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  customer: Object,
  agents: Array,
  offices: Array
});

const form = useForm({
  name: props.customer?.user?.name || '',
  email: props.customer?.user?.email || '',
  phone: props.customer?.phone || '',
  alternate_phone: props.customer?.alternate_phone || '',
  address: props.customer?.address || '',
  city: props.customer?.city || '',
  district: props.customer?.district || '',
  customer_type: props.customer?.customer_type || 'individual',
  tax_number: props.customer?.tax_number || '',
  tax_office: props.customer?.tax_office || '',
  company_name: props.customer?.company_name || '',
  lead_source: props.customer?.lead_source || '',
  lead_status: props.customer?.lead_status || 'new',
  notes: props.customer?.notes || '',
  next_followup: props.customer?.next_followup || '',
  preferences: props.customer?.preferences || '',
  agent_id: props.customer?.agent_id || '',
  office_id: props.customer?.office_id || '',
  is_active: props.customer?.is_active ?? true
});

const submit = () => {
  if (props.customer) {
    form.put(route('customers.update', props.customer.id));
  } else {
    form.post(route('customers.store'));
  }
};
</script> 