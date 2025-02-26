<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ property ? 'İlanı Düzenle' : 'Yeni İlan Ekle' }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <!-- Temel Bilgiler -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <InputLabel for="title" value="İlan Başlığı" />
                  <TextInput
                    id="title"
                    type="text"
                    v-model="form.title"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="type" value="İlan Tipi" />
                  <SelectInput
                    id="type"
                    v-model="form.type"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option value="Daire">Daire</option>
                    <option value="Villa">Villa</option>
                    <option value="Arsa">Arsa</option>
                    <option value="İşyeri">İşyeri</option>
                  </SelectInput>
                  <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="status" value="Durum" />
                  <SelectInput
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option value="Satılık">Satılık</option>
                    <option value="Kiralık">Kiralık</option>
                  </SelectInput>
                  <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="price" value="Fiyat" />
                  <TextInput
                    id="price"
                    type="number"
                    v-model="form.price"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors.price" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- Konum Bilgileri -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Konum Bilgileri</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <InputLabel for="city" value="Şehir" />
                  <SelectInput
                    id="city"
                    v-model="form.location.city"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option v-for="city in cities" :key="city" :value="city">
                      {{ city }}
                    </option>
                  </SelectInput>
                  <InputError :message="form.errors['location.city']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="district" value="İlçe" />
                  <SelectInput
                    id="district"
                    v-model="form.location.district"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option v-for="district in districts" :key="district" :value="district">
                      {{ district }}
                    </option>
                  </SelectInput>
                  <InputError :message="form.errors['location.district']" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                  <InputLabel for="address" value="Açık Adres" />
                  <TextArea
                    id="address"
                    v-model="form.location.address"
                    class="mt-1 block w-full"
                    rows="3"
                    required
                  />
                  <InputError :message="form.errors['location.address']" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- Detay Bilgileri -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Detay Bilgileri</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <InputLabel for="gross_sqm" value="Brüt m²" />
                  <TextInput
                    id="gross_sqm"
                    type="number"
                    v-model="form.details.gross_sqm"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors['details.gross_sqm']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="net_sqm" value="Net m²" />
                  <TextInput
                    id="net_sqm"
                    type="number"
                    v-model="form.details.net_sqm"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors['details.net_sqm']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="rooms" value="Oda Sayısı" />
                  <SelectInput
                    id="rooms"
                    v-model="form.details.rooms"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option value="1+0">1+0</option>
                    <option value="1+1">1+1</option>
                    <option value="2+1">2+1</option>
                    <option value="3+1">3+1</option>
                    <option value="4+1">4+1</option>
                    <option value="5+1">5+1</option>
                    <option value="6+1">6+1 ve üzeri</option>
                  </SelectInput>
                  <InputError :message="form.errors['details.rooms']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="floor" value="Bulunduğu Kat" />
                  <TextInput
                    id="floor"
                    type="number"
                    v-model="form.details.floor"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors['details.floor']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="age" value="Bina Yaşı" />
                  <TextInput
                    id="age"
                    type="number"
                    v-model="form.details.age"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors['details.age']" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="heating" value="Isıtma" />
                  <SelectInput
                    id="heating"
                    v-model="form.details.heating"
                    class="mt-1 block w-full"
                    required
                  >
                    <option value="">Seçiniz</option>
                    <option value="Doğalgaz">Doğalgaz</option>
                    <option value="Merkezi">Merkezi Sistem</option>
                    <option value="Klima">Klima</option>
                    <option value="Soba">Soba</option>
                  </SelectInput>
                  <InputError :message="form.errors['details.heating']" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- Açıklama -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Açıklama</h3>
              <div>
                <TextArea
                  v-model="form.description"
                  class="mt-1 block w-full"
                  rows="6"
                  required
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>
            </div>

            <!-- Medya -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Fotoğraflar</h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                  v-for="(image, index) in form.media.images"
                  :key="index"
                  class="relative"
                >
                  <img
                    :src="image.preview || image.url"
                    class="w-full h-32 object-cover rounded"
                  />
                  <button
                    type="button"
                    @click="removeImage(index)"
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                  >
                    <XMarkIcon class="w-4 h-4" />
                  </button>
                </div>
                <div
                  v-if="form.media.images.length < 10"
                  class="border-2 border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer hover:border-primary-500"
                  @click="$refs.fileInput.click()"
                >
                  <div class="text-center p-4">
                    <PlusIcon class="w-8 h-8 mx-auto text-gray-400" />
                    <span class="text-sm text-gray-500">Fotoğraf Ekle</span>
                  </div>
                  <input
                    type="file"
                    ref="fileInput"
                    @change="handleImageUpload"
                    accept="image/*"
                    multiple
                    class="hidden"
                  />
                </div>
              </div>
              <InputError :message="form.errors['media.images']" class="mt-2" />
            </div>

            <!-- Kaydet -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('properties.index')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
              >
                İptal
              </Link>
              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                {{ property ? 'Güncelle' : 'Kaydet' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  property: Object,
  cities: Array,
  districts: Array,
});

const form = useForm({
  title: props.property?.title || '',
  type: props.property?.type || '',
  status: props.property?.status || '',
  price: props.property?.price || '',
  description: props.property?.description || '',
  location: {
    city: props.property?.location?.city || '',
    district: props.property?.location?.district || '',
    address: props.property?.location?.address || '',
  },
  details: {
    gross_sqm: props.property?.details?.gross_sqm || '',
    net_sqm: props.property?.details?.net_sqm || '',
    rooms: props.property?.details?.rooms || '',
    floor: props.property?.details?.floor || '',
    age: props.property?.details?.age || '',
    heating: props.property?.details?.heating || '',
  },
  media: {
    images: props.property?.media?.images || [],
  },
});

const fileInput = ref(null);

const handleImageUpload = (e) => {
  const files = Array.from(e.target.files);
  const remainingSlots = 10 - form.media.images.length;
  
  files.slice(0, remainingSlots).forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      form.media.images.push({
        file: file,
        preview: e.target.result,
      });
    };
    reader.readAsDataURL(file);
  });
};

const removeImage = (index) => {
  form.media.images.splice(index, 1);
};

const submit = () => {
  if (props.property) {
    form.put(route('properties.update', props.property.id));
  } else {
    form.post(route('properties.store'));
  }
};
</script> 