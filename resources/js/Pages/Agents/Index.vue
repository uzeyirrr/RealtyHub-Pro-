<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Emlak Danışmanları
        </h2>
        <Link
          v-if="$page.props.auth.user.permissions.includes('create-agents')"
          :href="route('agents.create')"
          class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700"
        >
          Yeni Danışman
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtreler -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <InputLabel for="search" value="Arama" />
              <TextInput
                id="search"
                v-model="filters.search"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ad, telefon veya lisans no"
              />
            </div>
            <div>
              <InputLabel for="office" value="Ofis" />
              <SelectInput
                id="office"
                v-model="filters.office_id"
                class="mt-1 block w-full"
              >
                <option value="">Tüm Ofisler</option>
                <option v-for="office in offices" :key="office.id" :value="office.id">
                  {{ office.name }}
                </option>
              </SelectInput>
            </div>
            <div>
              <InputLabel for="status" value="Durum" />
              <SelectInput
                id="status"
                v-model="filters.status"
                class="mt-1 block w-full"
              >
                <option value="">Tümü</option>
                <option value="active">Aktif</option>
                <option value="inactive">Pasif</option>
              </SelectInput>
            </div>
          </div>
        </div>

        <!-- Danışman Listesi -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <div
              v-for="agent in agents.data"
              :key="agent.id"
              class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
            >
              <div class="p-4">
                <div class="flex items-center space-x-4">
                  <img
                    :src="agent.photo_url"
                    :alt="agent.user.name"
                    class="h-16 w-16 rounded-full object-cover"
                  />
                  <div>
                    <Link
                      :href="route('agents.show', agent.id)"
                      class="text-lg font-semibold text-gray-900 hover:text-primary-600"
                    >
                      {{ agent.user.name }}
                    </Link>
                    <p class="text-sm text-gray-500">{{ agent.title }}</p>
                    <p class="text-sm text-gray-500">{{ agent.office.name }}</p>
                  </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-gray-500">Aktif İlanlar</p>
                    <p class="font-semibold">{{ agent.stats.active_listings_count }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Aktif Müşteriler</p>
                    <p class="font-semibold">{{ agent.stats.active_customers_count }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Toplam Satış</p>
                    <p class="font-semibold">{{ agent.stats.total_sales }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Toplam Kiralama</p>
                    <p class="font-semibold">{{ agent.stats.total_rentals }}</p>
                  </div>
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                  <Link
                    v-if="$page.props.auth.user.permissions.includes('edit-agents')"
                    :href="route('agents.edit', agent.id)"
                    class="inline-flex items-center px-3 py-1 bg-gray-100 border border-transparent rounded-md text-xs text-gray-600 hover:bg-gray-200"
                  >
                    Düzenle
                  </Link>
                  <button
                    v-if="$page.props.auth.user.permissions.includes('delete-agents')"
                    @click="confirmAgentDeletion(agent)"
                    class="inline-flex items-center px-3 py-1 bg-red-100 border border-transparent rounded-md text-xs text-red-600 hover:bg-red-200"
                  >
                    Sil
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4">
            <Pagination :links="agents.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- Silme Onay Modalı -->
    <Modal :show="confirmingAgentDeletion" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">
          Danışmanı Silmek İstediğinize Emin Misiniz?
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Bu işlem geri alınamaz. Danışmanın tüm bilgileri silinecektir.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="closeModal">İptal</SecondaryButton>
          <DangerButton
            :class="{ 'opacity-25': processing }"
            :disabled="processing"
            @click="deleteAgent"
          >
            Danışmanı Sil
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
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  agents: Object,
  offices: Array,
  filters: Object
});

const filters = ref({
  search: props.filters.search || '',
  office_id: props.filters.office_id || '',
  status: props.filters.status || ''
});

const confirmingAgentDeletion = ref(false);
const processing = ref(false);
const agentToDelete = ref(null);

watch(filters.value, (value) => {
  router.get(route('agents.index'), value, {
    preserveState: true,
    preserveScroll: true,
  });
}, { deep: true });

const confirmAgentDeletion = (agent) => {
  agentToDelete.value = agent;
  confirmingAgentDeletion.value = true;
};

const closeModal = () => {
  confirmingAgentDeletion.value = false;
  processing.value = false;
  agentToDelete.value = null;
};

const deleteAgent = () => {
  if (!agentToDelete.value) return;

  processing.value = true;
  router.delete(route('agents.destroy', agentToDelete.value.id), {
    onSuccess: () => closeModal(),
    onError: () => processing.value = false,
  });
};
</script> 