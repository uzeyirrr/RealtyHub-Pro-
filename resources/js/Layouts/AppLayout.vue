<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import MainMenu from '@/Components/MainMenu.vue';
import { 
  Bars3Icon,
  XMarkIcon,
  UserCircleIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;
</script>

<template>
  <div>
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo ve Mobil Menü -->
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')" class="text-xl font-bold text-gray-800">
                RealtyHub Pro
              </Link>
            </div>
          </div>

          <!-- Sağ Menü -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="ml-3 relative">
              <div class="flex items-center space-x-4">
                <span class="text-gray-700">{{ user.name }}</span>
                <Link
                  :href="route('profile.edit')"
                  class="text-gray-500 hover:text-gray-700"
                >
                  <UserCircleIcon class="h-6 w-6" />
                </Link>
              </div>
            </div>
          </div>

          <!-- Mobil Menü Butonu -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
            >
              <Bars3Icon
                v-if="!showingNavigationDropdown"
                class="h-6 w-6"
              />
              <XMarkIcon
                v-else
                class="h-6 w-6"
              />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobil Menü -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <MainMenu />
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
          <div class="px-4">
            <div class="font-medium text-base text-gray-800">
              {{ user.name }}
            </div>
            <div class="font-medium text-sm text-gray-500">
              {{ user.email }}
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <Link
              :href="route('profile.edit')"
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
            >
              Profil
            </Link>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
            >
              Çıkış Yap
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Ana İçerik -->
    <div class="min-h-screen bg-gray-100">
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Sol Menü -->
          <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <div class="hidden lg:block lg:col-span-3">
              <nav class="sticky top-4">
                <MainMenu />
              </nav>
            </div>
            <!-- Ana İçerik -->
            <main class="lg:col-span-9">
              <slot />
            </main>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 