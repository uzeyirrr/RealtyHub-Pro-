<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  role: {
    type: Object,
    required: false,
    default: () => ({
      id: null,
      name: '',
      permissions: []
    })
  },
  permissions: {
    type: Array,
    required: true,
    default: () => []
  }
});

const form = useForm({
  name: props.role.name,
  permissions: props.role.permissions
});

const submit = () => {
  if (props.role.id) {
    form.put(route('roles.update', props.role.id));
  } else {
    form.post(route('roles.store'));
  }
};
</script>

<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ role.id ? 'Rol Düzenle' : 'Yeni Rol Ekle' }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <InputLabel for="name" value="Rol Adı" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  required
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">İzinler</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div v-for="permission in permissions" :key="permission.id" class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        :id="'permission-' + permission.id"
                        type="checkbox"
                        :value="permission.id"
                        v-model="form.permissions"
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label :for="'permission-' + permission.id" class="font-medium text-gray-700">
                        {{ permission.name }}
                      </label>
                      <p class="text-gray-500">{{ permission.description }}</p>
                    </div>
                  </div>
                </div>
                <InputError class="mt-2" :message="form.errors.permissions" />
              </div>

              <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  {{ role.id ? 'Güncelle' : 'Kaydet' }}
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template> 