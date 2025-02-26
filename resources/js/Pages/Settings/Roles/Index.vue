<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Roller
        </h2>
        <Link
          :href="route('roles.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Yeni Rol Ekle
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rol Adı
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Açıklama
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kullanıcı Sayısı
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      İzin Sayısı
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                      <span class="sr-only">İşlemler</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="role in roles" :key="role.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ role.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ role.slug }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ role.description || '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ role.users_count }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ role.permissions_count }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link
                          :href="route('roles.show', role.id)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Detay
                        </Link>
                        <Link
                          :href="route('roles.edit', role.id)"
                          class="text-yellow-600 hover:text-yellow-900"
                        >
                          Düzenle
                        </Link>
                        <button
                          v-if="role.slug !== 'admin' && role.users_count === 0"
                          @click="confirmDelete(role)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Sil
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Silme Onay Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Rolü Sil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Bu rolü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
        </p>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="closeDeleteModal"
          >
            İptal
          </button>
          <button
            type="button"
            class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            @click="deleteRole"
          >
            Sil
          </button>
        </div>
      </div>
    </Modal>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  roles: Array,
});

const showDeleteModal = ref(false);
const roleToDelete = ref(null);

const confirmDelete = (role) => {
  roleToDelete.value = role;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  roleToDelete.value = null;
};

const deleteRole = () => {
  router.delete(route('roles.destroy', roleToDelete.value.id), {
    onSuccess: () => {
      closeDeleteModal();
    },
  });
};
</script> 