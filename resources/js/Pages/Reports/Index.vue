<template>
  <AppLayout title="Raporlar">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Raporlar
        </h2>
        <div class="flex space-x-4">
          <Link
            :href="route('reports.custom-date-range')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Özel Tarih Aralığı
          </Link>
          <button
            @click="showExportModal = true"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Excel'e Aktar
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Özet İstatistikler -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Özet İstatistikler
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">İlanlar</h4>
              <div class="mt-2 space-y-2">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Toplam İlan</span>
                  <span class="font-medium">{{ summary.total_properties }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Aktif İlan</span>
                  <span class="font-medium">{{ summary.active_properties }}</span>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Müşteriler</h4>
              <div class="mt-2 space-y-2">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Toplam Müşteri</span>
                  <span class="font-medium">{{ summary.total_customers }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Aktif Müşteri</span>
                  <span class="font-medium">{{ summary.active_customers }}</span>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Danışmanlar</h4>
              <div class="mt-2 space-y-2">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Toplam Danışman</span>
                  <span class="font-medium">{{ summary.total_agents }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Aktif Danışman</span>
                  <span class="font-medium">{{ summary.active_agents }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bu Ayki İstatistikler -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Bu Ayki İstatistikler
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Yeni İlanlar</h4>
              <p class="mt-2 text-2xl font-semibold">
                {{ summary.monthly_stats.new_properties }}
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Yeni Müşteriler</h4>
              <p class="mt-2 text-2xl font-semibold">
                {{ summary.monthly_stats.new_customers }}
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Satılan İlanlar</h4>
              <p class="mt-2 text-2xl font-semibold">
                {{ summary.monthly_stats.properties_sold }}
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Kiralanan İlanlar</h4>
              <p class="mt-2 text-2xl font-semibold">
                {{ summary.monthly_stats.properties_rented }}
              </p>
            </div>
          </div>
        </div>

        <!-- İlan İstatistikleri -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            İlan İstatistikleri
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                İlan Tipleri
              </h4>
              <div class="space-y-2">
                <div
                  v-for="(count, type) in propertyStats.by_type"
                  :key="type"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ type }}</span>
                  <span class="font-medium">{{ count }}</span>
                </div>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                İlan Durumları
              </h4>
              <div class="space-y-2">
                <div
                  v-for="(count, status) in propertyStats.by_status"
                  :key="status"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ status }}</span>
                  <span class="font-medium">{{ count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Müşteri İstatistikleri -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Müşteri İstatistikleri
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Müşteri Tipleri
              </h4>
              <div class="space-y-2">
                <div
                  v-for="(count, type) in customerStats.by_type"
                  :key="type"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ type }}</span>
                  <span class="font-medium">{{ count }}</span>
                </div>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Lead Durumları
              </h4>
              <div class="space-y-2">
                <div
                  v-for="(count, status) in customerStats.by_lead_status"
                  :key="status"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ status }}</span>
                  <span class="font-medium">{{ count }}</span>
                </div>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Lead Kaynakları
              </h4>
              <div class="space-y-2">
                <div
                  v-for="(count, source) in customerStats.by_lead_source"
                  :key="source"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ source }}</span>
                  <span class="font-medium">{{ count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Danışman İstatistikleri -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Danışman İstatistikleri
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                En İyi Performans Gösteren Danışmanlar
              </h4>
              <div class="space-y-2">
                <div
                  v-for="agent in agentStats.top_performers"
                  :key="agent.id"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ agent.user.name }}</span>
                  <span class="font-medium">{{ agent.properties_count }} İşlem</span>
                </div>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                En Aktif Danışmanlar
              </h4>
              <div class="space-y-2">
                <div
                  v-for="agent in agentStats.most_active"
                  :key="agent.id"
                  class="flex justify-between items-center"
                >
                  <span class="text-sm text-gray-600">{{ agent.user.name }}</span>
                  <span class="font-medium">
                    {{ agent.properties_count }} İlan / {{ agent.customers_count }} Müşteri
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Excel Export Modal -->
    <Modal :show="showExportModal" @close="showExportModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Excel Raporu Oluştur
        </h2>
        <form @submit.prevent="exportExcel" class="space-y-4">
          <div>
            <InputLabel for="report_type" value="Rapor Tipi" />
            <SelectInput
              id="report_type"
              v-model="form.report_type"
              class="mt-1 block w-full"
            >
              <option value="properties">İlan Raporu</option>
              <option value="customers">Müşteri Raporu</option>
              <option value="agents">Danışman Raporu</option>
            </SelectInput>
          </div>
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
          <div class="flex justify-end space-x-2">
            <SecondaryButton @click="showExportModal = false">
              İptal
            </SecondaryButton>
            <PrimaryButton :disabled="form.processing">
              {{ form.processing ? 'İşleniyor...' : 'İndir' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  summary: Object,
  propertyStats: Object,
  customerStats: Object,
  agentStats: Object,
})

const showExportModal = ref(false)

const form = useForm({
  report_type: 'properties',
  start_date: '',
  end_date: '',
})

const exportExcel = () => {
  form.post(route('reports.export-excel'), {
    preserveScroll: true,
    onSuccess: () => {
      showExportModal.value = false
      form.reset()
    },
  })
}
</script> 