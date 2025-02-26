<template>
  <MainLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ agent ? 'Danışman Düzenle' : 'Yeni Danışman' }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <!-- Temel Bilgiler -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <InputLabel for="name" value="Ad Soyad" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="email" value="E-posta" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.email" class="mt-2" />
              </div>

              <div>
                <InputLabel for="phone" value="Telefon" />
                <TextInput
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.phone" class="mt-2" />
              </div>

              <div>
                <InputLabel for="title" value="Ünvan" />
                <TextInput
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
              </div>

              <div>
                <InputLabel for="office_id" value="Ofis" />
                <SelectInput
                  id="office_id"
                  v-model="form.office_id"
                  class="mt-1 block w-full"
                  required
                >
                  <option value="">Ofis Seçin</option>
                  <option
                    v-for="office in offices"
                    :key="office.id"
                    :value="office.id"
                  >
                    {{ office.name }}
                  </option>
                </SelectInput>
                <InputError :message="form.errors.office_id" class="mt-2" />
              </div>

              <div>
                <InputLabel for="commission_rate" value="Komisyon Oranı (%)" />
                <TextInput
                  id="commission_rate"
                  v-model="form.commission_rate"
                  type="number"
                  step="0.01"
                  min="0"
                  max="100"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.commission_rate" class="mt-2" />
              </div>
            </div>

            <!-- Lisans Bilgileri -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <InputLabel for="license_number" value="Lisans Numarası" />
                <TextInput
                  id="license_number"
                  v-model="form.license_number"
                  type="text"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.license_number" class="mt-2" />
              </div>

              <div>
                <InputLabel for="license_date" value="Lisans Tarihi" />
                <TextInput
                  id="license_date"
                  v-model="form.license_date"
                  type="date"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.license_date" class="mt-2" />
              </div>
            </div>

            <!-- Uzmanlık ve Dil -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <InputLabel value="Uzmanlık Alanları" />
                <div class="mt-2 space-y-2">
                  <label
                    v-for="specialization in specializations"
                    :key="specialization"
                    class="inline-flex items-center mr-4"
                  >
                    <Checkbox
                      :value="specialization"
                      v-model:checked="form.specializations"
                    />
                    <span class="ml-2 text-sm text-gray-600">{{ specialization }}</span>
                  </label>
                </div>
                <InputError :message="form.errors.specializations" class="mt-2" />
              </div>

              <div>
                <InputLabel value="Yabancı Diller" />
                <div class="mt-2 space-y-2">
                  <label
                    v-for="language in languages"
                    :key="language"
                    class="inline-flex items-center mr-4"
                  >
                    <Checkbox
                      :value="language"
                      v-model:checked="form.languages"
                    />
                    <span class="ml-2 text-sm text-gray-600">{{ language }}</span>
                  </label>
                </div>
                <InputError :message="form.errors.languages" class="mt-2" />
              </div>
            </div>

            <!-- Çalışma Bölgeleri -->
            <div class="mb-8">
              <InputLabel value="Çalışma Bölgeleri" />
              <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                <label
                  v-for="area in workingAreas"
                  :key="area"
                  class="inline-flex items-center"
                >
                  <Checkbox
                    :value="area"
                    v-model:checked="form.working_areas"
                  />
                  <span class="ml-2 text-sm text-gray-600">{{ area }}</span>
                </label>
              </div>
              <InputError :message="form.errors.working_areas" class="mt-2" />
            </div>

            <!-- Hakkında -->
            <div class="mb-8">
              <InputLabel for="about" value="Hakkında" />
              <TextArea
                id="about"
                v-model="form.about"
                class="mt-1 block w-full"
                rows="4"
              />
              <InputError :message="form.errors.about" class="mt-2" />
            </div>

            <!-- Profil Fotoğrafı -->
            <div class="mb-8">
              <InputLabel for="photo" value="Profil Fotoğrafı" />
              <input
                type="file"
                @input="form.photo = $event.target.files[0]"
                accept="image/*"
                class="mt-1 block w-full"
              />
              <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Durum -->
            <div class="mb-8">
              <label class="flex items-center">
                <Checkbox v-model:checked="form.is_active" />
                <span class="ml-2 text-sm text-gray-600">Aktif</span>
              </label>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end space-x-4">
              <Link
                :href="route('agents.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200"
              >
                İptal
              </Link>
              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ agent ? 'Güncelle' : 'Kaydet' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  agent: Object,
  offices: Array,
  specializations: Array,
  languages: Array,
  workingAreas: Array,
});

const form = useForm({
  name: props.agent?.user?.name || '',
  email: props.agent?.user?.email || '',
  phone: props.agent?.phone || '',
  title: props.agent?.title || '',
  office_id: props.agent?.office_id || '',
  commission_rate: props.agent?.commission_rate || 0,
  license_number: props.agent?.license_number || '',
  license_date: props.agent?.license_date || '',
  specializations: props.agent?.specializations || [],
  languages: props.agent?.languages || [],
  working_areas: props.agent?.working_areas || [],
  about: props.agent?.about || '',
  photo: null,
  is_active: props.agent?.is_active ?? true,
});

const submit = () => {
  if (props.agent) {
    form.put(route('agents.update', props.agent.id));
  } else {
    form.post(route('agents.store'));
  }
};
</script> 