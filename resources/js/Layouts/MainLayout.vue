<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
          <!-- Logo ve Ana Menü -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <Link :href="route('dashboard')" class="flex items-center">
                <img src="/images/logo.png" alt="RealtyHub Pro" class="h-12 w-auto" />
                <span class="ml-3 text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">
                  RealtyHub Pro
                </span>
              </Link>
            </div>
            
            <!-- Ana Menü -->
            <div class="hidden md:ml-10 md:flex md:space-x-8">
              <NavLink 
                v-for="item in navigationItems" 
                :key="item.name"
                :href="route(item.route)"
                :active="route().current(item.route + '*')"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium transition-colors duration-200"
                :class="[
                  route().current(item.route + '*')
                    ? 'text-primary-600 border-b-2 border-primary-500'
                    : 'text-gray-500 hover:text-gray-900 hover:border-b-2 hover:border-gray-300'
                ]"
              >
                <component 
                  :is="item.icon" 
                  class="h-5 w-5 mr-2"
                  :class="route().current(item.route + '*') ? 'text-primary-500' : 'text-gray-400'"
                />
                {{ item.name }}
              </NavLink>
            </div>
          </div>

          <!-- Sağ Menü -->
          <div class="hidden md:flex md:items-center md:space-x-6">
            <!-- Bildirimler -->
            <button class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-full">
              <span class="sr-only">Bildirimler</span>
              <BellIcon class="h-6 w-6" />
            </button>

            <!-- Kullanıcı Menüsü -->
            <div class="relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out group">
                    <img 
                      :src="$page.props.auth.user.profile_photo_url || '/images/default-avatar.png'" 
                      :alt="$page.props.auth.user.name"
                      class="h-8 w-8 rounded-full object-cover border-2 border-transparent group-hover:border-primary-500 transition-colors duration-200"
                    />
                    <div class="ml-2">
                      <div class="text-sm font-medium text-gray-700">
                        {{ $page.props.auth.user.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ $page.props.auth.user.email }}
                      </div>
                    </div>
                    <ChevronDownIcon class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-600" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('profile.edit')" class="flex items-center">
                    <UserIcon class="mr-2 h-5 w-5 text-gray-400" />
                    Profil
                  </DropdownLink>
                  <DropdownLink :href="route('settings.index')" class="flex items-center">
                    <Cog6ToothIcon class="mr-2 h-5 w-5 text-gray-400" />
                    Ayarlar
                  </DropdownLink>
                  <div class="border-t border-gray-200"></div>
                  <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center text-red-600 hover:text-red-700">
                    <ArrowRightOnRectangleIcon class="mr-2 h-5 w-5" />
                    Çıkış Yap
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Mobil Menü Butonu -->
          <div class="flex items-center md:hidden">
            <button 
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
            >
              <span class="sr-only">Menüyü Aç</span>
              <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobil Menü -->
      <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink 
            v-for="item in navigationItems" 
            :key="item.name"
            :href="route(item.route)"
            :active="route().current(item.route + '*')"
          >
            <component :is="item.icon" class="mr-2 h-5 w-5" :class="route().current(item.route + '*') ? 'text-primary-500' : 'text-gray-400'" />
            {{ item.name }}
          </ResponsiveNavLink>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <img 
                :src="$page.props.auth.user.profile_photo_url || '/images/default-avatar.png'" 
                :alt="$page.props.auth.user.name"
                class="h-10 w-10 rounded-full object-cover"
              />
            </div>
            <div class="ml-3">
              <div class="font-medium text-base text-gray-800">
                {{ $page.props.auth.user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">
                {{ $page.props.auth.user.email }}
              </div>
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')" class="flex items-center">
              <UserIcon class="mr-2 h-5 w-5 text-gray-400" />
              Profil
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('settings.index')" class="flex items-center">
              <Cog6ToothIcon class="mr-2 h-5 w-5 text-gray-400" />
              Ayarlar
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="flex items-center text-red-600">
              <ArrowRightOnRectangleIcon class="mr-2 h-5 w-5" />
              Çıkış Yap
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Ana İçerik -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { 
  Bars3Icon, 
  XMarkIcon,
  BellIcon,
  UserIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  ChevronDownIcon,
  HomeIcon,
  BuildingOfficeIcon,
  UsersIcon,
  UserGroupIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);

const navigationItems = [
  {
    name: 'Dashboard',
    icon: HomeIcon,
    route: 'dashboard'
  },
  {
    name: 'İlanlar',
    icon: BuildingOfficeIcon,
    route: 'properties.index'
  },
  {
    name: 'Danışmanlar',
    icon: UsersIcon,
    route: 'agents.index'
  },
  {
    name: 'Müşteriler',
    icon: UserGroupIcon,
    route: 'customers.index'
  },
  {
    name: 'Raporlar',
    icon: ChartBarIcon,
    route: 'reports.index'
  }
];
</script> 