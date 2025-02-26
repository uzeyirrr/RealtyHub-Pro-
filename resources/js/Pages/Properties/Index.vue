<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          İlanlar
        </h2>
        <Link
          v-if="$page.props.auth.user.permissions.includes('create-listings')"
          :href="route('properties.create')"
          class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700"
        >
          Yeni İlan Ekle
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtreler -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">İlan Tipi</label>
              <select v-model="filters.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tümü</option>
                <option value="Daire">Daire</option>
                <option value="Villa">Villa</option>
                <option value="Arsa">Arsa</option>
                <option value="İşyeri">İşyeri</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Durum</label>
              <select v-model="filters.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tümü</option>
                <option value="Satılık">Satılık</option>
                <option value="Kiralık">Kiralık</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Sıralama</label>
              <select v-model="filters.sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="newest">En Yeni</option>
                <option value="price_asc">Fiyat (Artan)</option>
                <option value="price_desc">Fiyat (Azalan)</option>
                <option value="views">En Çok Görüntülenen</option>
              </select>
            </div>
          </div>
        </div>

        <!-- İlan Listesi -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="property in properties.data" :key="property.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- İlan Resmi -->
                <div class="relative h-48">
                  <img :src="property.media.main_image" :alt="property.title" class="w-full h-full object-cover">
                  <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded" :class="{
                      'bg-green-100 text-green-800': property.status === 'Satılık',
                      'bg-blue-100 text-blue-800': property.status === 'Kiralık'
                    }">
                      {{ property.status }}
                    </span>
                  </div>
                </div>

                <!-- İlan Detayları -->
                <div class="p-4">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ property.title }}</h3>
                  <p class="text-sm text-gray-600 mb-4">{{ property.location.district }}/{{ property.location.city }}</p>
                  
                  <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-bold text-primary-600">{{ property.formatted_price }}</span>
                    <span class="text-sm text-gray-500">{{ property.type }}</span>
                  </div>

                  <div class="flex justify-between text-sm text-gray-500 mb-4">
                    <span>{{ property.details.rooms }} Oda</span>
                    <span>{{ property.details.gross_sqm }}m²</span>
                    <span>{{ property.details.floor }}. Kat</span>
                  </div>

                  <!-- Aksiyonlar -->
                  <div class="flex justify-between items-center">
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-primary-600 hover:text-primary-700 font-medium"
                    >
                      Detayları Gör
                    </Link>
                    <div class="flex space-x-2" v-if="$page.props.auth.user.permissions.includes('edit-listings')">
                      <Link
                        :href="route('properties.edit', property.id)"
                        class="text-gray-600 hover:text-gray-700"
                      >
                        <PencilIcon class="h-5 w-5" />
                      </Link>
                      <button
                        v-if="$page.props.auth.user.permissions.includes('delete-listings')"
                        @click="confirmDelete(property)"
                        class="text-red-600 hover:text-red-700"
                      >
                        <TrashIcon class="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="properties.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Silme Onay Modalı -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          İlanı Silmek İstediğinize Emin misiniz?
        </h2>
        <p class="text-sm text-gray-600 mb-4">
          Bu işlem geri alınamaz.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="showDeleteModal = false">İptal</SecondaryButton>
          <DangerButton
            :class="{ 'opacity-25': processing }"
            :disabled="processing"
            @click="deleteProperty"
          >
            Sil
          </DangerButton>
        </div>
      </div>
    </Modal>
  </MainLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  properties: Object,
  filters: Object
});

const filters = ref({
  type: props.filters?.type || '',
  status: props.filters?.status || '',
  sort: props.filters?.sort || 'newest'
});

const showDeleteModal = ref(false);
const processing = ref(false);
const propertyToDelete = ref(null);

watch(filters, (newFilters) => {
  router.get(route('properties.index'), newFilters, {
    preserveState: true,
    preserveScroll: true,
  });
}, { deep: true });

const confirmDelete = (property) => {
  propertyToDelete.value = property;
  showDeleteModal.value = true;
};

const deleteProperty = () => {
  if (!propertyToDelete.value) return;
  
  processing.value = true;
  router.delete(route('properties.destroy', propertyToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      processing.value = false;
      propertyToDelete.value = null;
    },
    onError: () => {
      processing.value = false;
    },
  });
};
</script> 