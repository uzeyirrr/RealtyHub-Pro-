<template>
  <AppLayout title="Özel Tarih Aralığı Raporu">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Özel Tarih Aralığı Raporu
        </h2>
        <Link
          :href="route('reports.index')"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        >
          Raporlara Dön
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Tarih Aralığı Seçimi -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <form @submit.prevent="getReport" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel for="start_date" value="Başlangıç Tarihi" />
                <TextInput
                  id="start_date"
                  type="date"
                  v-model="form.start_date"
                  class="mt-1 block w-full"
                  required
                />
              </div>
              <div>
                <InputLabel for="end_date" value="Bitiş Tarihi" />
                <TextInput
                  id="end_date"
                  type="date"
                  v-model="form.end_date"
                  class="mt-1 block w-full"
                  required
                />
              </div>
            </div>
            <div class="flex justify-end">
              <PrimaryButton :disabled="form.processing">
                {{ form.processing ? 'İşleniyor...' : 'Raporu Getir' }}
              </PrimaryButton>
            </div>
          </form>
        </div>

        <!-- İstatistikler -->
        <div v-if="stats" class="space-y-6">
          <!-- Özet İstatistikler -->
          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ dateRange.start }} - {{ dateRange.end }} Tarihleri Arası İstatistikler
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">İlanlar</h4>
                <div class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Yeni İlanlar</span>
                    <span class="font-medium">{{ stats.new_properties }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Satılan İlanlar</span>
                    <span class="font-medium">{{ stats.properties_sold }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Kiralanan İlanlar</span>
                    <span class="font-medium">{{ stats.properties_rented }}</span>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Müşteriler</h4>
                <div class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Yeni Müşteriler</span>
                    <span class="font-medium">{{ stats.new_customers }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Dönüştürülen Lead'ler</span>
                    <span class="font-medium">{{ stats.converted_leads }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  dateRange: Object,
  stats: Object,
})

const form = useForm({
  start_date: props.dateRange?.start || '',
  end_date: props.dateRange?.end || '',
})

const getReport = () => {
  form.get(route('reports.custom-date-range'), {
    preserveState: true,
    preserveScroll: true,
  })
}
</script> 