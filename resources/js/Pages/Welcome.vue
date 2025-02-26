<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  MagnifyingGlassIcon,
  BuildingOfficeIcon,
  HomeIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline';

defineProps({
  featuredProperties: {
    type: Object,
    required: true
  },
  cities: {
    type: Array,
    required: true
  }
});

const filters = ref({
  city: '',
  type: '',
  priceRange: ''
});

const search = () => {
  console.log('Arama filtreleri:', filters.value);
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price);
};
</script>

<template>
  <Head>
    <title>Yezuri Emlak - Hayalinizdeki Eve Hoş Geldiniz</title>
  </Head>

  <div class="min-h-screen relative">
    <!-- Arkaplan Görüntüsü -->
    <div class="absolute inset-0 z-0">
      <div class="absolute inset-0 bg-gradient-to-r from-primary-900/90 to-primary-800/80"></div>
      <img src="/images/hero-bg.jpg" alt="Hero Background" class="w-full h-full object-cover" />
    </div>

    <!-- Header -->
    <header class="relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center">
            <BuildingOfficeIcon class="h-10 w-10 text-white" />
            <h1 class="ml-3 text-3xl font-bold text-white">Yezuri Emlak</h1>
          </div>
          <div class="flex space-x-6">
            <Link
              href="/login"
              class="text-white/80 hover:text-white transition-colors font-medium"
            >
              Giriş Yap
            </Link>
            <Link
              href="/register"
              class="bg-white text-primary-600 px-4 py-2 rounded-lg hover:bg-primary-50 transition-colors font-medium"
            >
              Kayıt Ol
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Content -->
    <main class="relative z-10 flex items-center justify-center min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-32">
        <div class="text-center mb-12">
          <h2 class="text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
            <span class="block">Hayalinizdeki Evi</span>
            <span class="block">Keşfedin</span>
          </h2>
          <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto">
            Binlerce kaliteli ilan arasından size en uygun evi bulun
          </p>
        </div>

        <!-- Arama Formu -->
        <div class="max-w-4xl mx-auto">
          <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <select
                  v-model="filters.city"
                  class="w-full rounded-xl border-0 bg-white/80 backdrop-blur-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 h-12"
                >
                  <option value="">Şehir Seçin</option>
                  <option v-for="city in cities" :key="city" :value="city">
                    {{ city }}
                  </option>
                </select>
              </div>
              <div>
                <select
                  v-model="filters.type"
                  class="w-full rounded-xl border-0 bg-white/80 backdrop-blur-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 h-12"
                >
                  <option value="">Emlak Tipi</option>
                  <option value="apartment">Daire</option>
                  <option value="villa">Villa</option>
                  <option value="land">Arsa</option>
                  <option value="commercial">İşyeri</option>
                </select>
              </div>
              <div>
                <select
                  v-model="filters.priceRange"
                  class="w-full rounded-xl border-0 bg-white/80 backdrop-blur-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 h-12"
                >
                  <option value="">Fiyat Aralığı</option>
                  <option value="0-500000">0 - 500.000 ₺</option>
                  <option value="500000-1000000">500.000 - 1.000.000 ₺</option>
                  <option value="1000000-2000000">1.000.000 - 2.000.000 ₺</option>
                  <option value="2000000+">2.000.000 ₺ ve üzeri</option>
                </select>
              </div>
            </div>
            <button
              @click="search"
              class="mt-6 w-full bg-primary-600 text-white h-12 rounded-xl hover:bg-primary-700 transition-colors flex items-center justify-center text-lg font-medium"
            >
              <MagnifyingGlassIcon class="h-6 w-6 mr-2" />
              Emlak Ara
            </button>
          </div>

          <!-- İstatistikler -->
          <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">10.000+</div>
              <div class="text-white/80">Aktif İlan</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">5.000+</div>
              <div class="text-white/80">Mutlu Müşteri</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">50+</div>
              <div class="text-white/80">Şehirde Hizmet</div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
