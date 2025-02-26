<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          İlan Detayı
        </h2>
        <div class="flex space-x-3">
          <Link
            v-if="$page.props.auth.user.permissions.includes('edit-listings')"
            :href="route('properties.edit', property.id)"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700"
          >
            Düzenle
          </Link>
          <button
            v-if="$page.props.auth.user.permissions.includes('delete-listings')"
            @click="confirmDelete"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
          >
            Sil
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Ana Bilgiler -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Fotoğraf Galerisi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="relative aspect-w-16 aspect-h-9 mb-4">
                  <img
                    :src="currentImage"
                    :alt="property.title"
                    class="object-cover rounded-lg"
                  />
                  <div class="absolute top-2 right-2">
                    <span
                      class="px-3 py-1 text-sm font-semibold rounded-full"
                      :class="{
                        'bg-green-100 text-green-800': property.status === 'Satılık',
                        'bg-blue-100 text-blue-800': property.status === 'Kiralık'
                      }"
                    >
                      {{ property.status }}
                    </span>
                  </div>
                </div>
                <div class="grid grid-cols-5 gap-2">
                  <button
                    v-for="(image, index) in property.media.images"
                    :key="index"
                    @click="currentImageIndex = index"
                    class="relative aspect-w-1 aspect-h-1"
                  >
                    <img
                      :src="image.url"
                      :alt="`${property.title} - ${index + 1}`"
                      class="object-cover rounded"
                      :class="{ 'ring-2 ring-primary-500': currentImageIndex === index }"
                    />
                  </button>
                </div>
              </div>
            </div>

            <!-- İlan Detayları -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ property.title }}</h3>
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <span class="text-3xl font-bold text-primary-600">{{ property.formatted_price }}</span>
                    <span class="text-lg text-gray-500">{{ property.type }}</span>
                  </div>
                  <div class="flex items-center space-x-2 text-gray-500">
                    <EyeIcon class="w-5 h-5" />
                    <span>{{ property.stats.view_count }} görüntülenme</span>
                  </div>
                </div>
                <div class="prose max-w-none">
                  <p class="whitespace-pre-line">{{ property.description }}</p>
                </div>
              </div>
            </div>

            <!-- Özellikler -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Özellikler</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                  <div class="flex items-center space-x-2">
                    <HomeIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Oda Sayısı</p>
                      <p class="font-medium">{{ property.details.rooms }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Square2StackIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Brüt Alan</p>
                      <p class="font-medium">{{ property.details.gross_sqm }}m²</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Square3Stack3DIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Net Alan</p>
                      <p class="font-medium">{{ property.details.net_sqm }}m²</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <BuildingOfficeIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Bulunduğu Kat</p>
                      <p class="font-medium">{{ property.details.floor }}. Kat</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <CalendarIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Bina Yaşı</p>
                      <p class="font-medium">{{ property.details.age }} Yıl</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <FireIcon class="w-5 h-5 text-gray-400" />
                    <div>
                      <p class="text-sm text-gray-500">Isıtma</p>
                      <p class="font-medium">{{ property.details.heating }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Yan Panel -->
          <div class="space-y-6">
            <!-- Konum -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Konum</h3>
                <div class="space-y-3">
                  <div>
                    <p class="text-sm text-gray-500">Şehir</p>
                    <p class="font-medium">{{ property.location.city }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">İlçe</p>
                    <p class="font-medium">{{ property.location.district }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Adres</p>
                    <p class="font-medium">{{ property.location.address }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Danışman Bilgileri -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Danışman</h3>
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <img
                      v-if="property.agent.photo"
                      :src="property.agent.photo"
                      :alt="property.agent.name"
                      class="h-12 w-12 rounded-full object-cover"
                    />
                    <div
                      v-else
                      class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center"
                    >
                      <UserIcon class="h-6 w-6 text-gray-400" />
                    </div>
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900">{{ property.agent.name }}</h4>
                    <p class="text-sm text-gray-500">{{ property.agent.phone }}</p>
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <Link
                    :href="route('agents.show', property.agent.id)"
                    class="text-primary-600 hover:text-primary-700 font-medium text-sm"
                  >
                    Danışman Profili
                  </Link>
                </div>
              </div>
            </div>

            <!-- İstatistikler -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">İstatistikler</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-500">İlan Tarihi</span>
                    <span class="font-medium">{{ formatDate(property.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Son Güncelleme</span>
                    <span class="font-medium">{{ formatDate(property.updated_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Görüntülenme</span>
                    <span class="font-medium">{{ property.stats.view_count }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Favori Eklenme</span>
                    <span class="font-medium">{{ property.stats.favorite_count }}</span>
                  </div>
                </div>
              </div>
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
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import {
  HomeIcon,
  Square2StackIcon,
  Square3Stack3DIcon,
  BuildingOfficeIcon,
  CalendarIcon,
  FireIcon,
  UserIcon,
  EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  property: Object,
});

const currentImageIndex = ref(0);
const showDeleteModal = ref(false);
const processing = ref(false);

const currentImage = computed(() => {
  return props.property.media.images[currentImageIndex.value]?.url;
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteProperty = () => {
  processing.value = true;
  router.delete(route('properties.destroy', props.property.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    },
  });
};
</script> 