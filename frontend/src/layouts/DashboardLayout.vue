<template>
  <div class="min-h-screen flex bg-background dark:bg-background-dark text-slate-900 transition-colors duration-500" :class="{ 'dark': isDark }">
    <!-- Overlay for Mobile Sidebar -->
    <transition name="fade">
      <div 
        v-if="isSidebarOpen" 
        @click="isSidebarOpen = false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-40 lg:hidden"
      ></div>
    </transition>

    <!-- Sidebar -->
    <aside 
      class="fixed inset-y-0 left-0 w-72 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-r border-slate-200/50 dark:border-slate-800/50 z-50 transition-all duration-500 ease-in-out transform lg:translate-x-0 lg:static lg:inset-0 shadow-2xl lg:shadow-none"
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="h-full flex flex-col relative overflow-hidden">
        <!-- Abstract Decorative Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
        
        <!-- Sidebar Brand -->
        <div class="p-8 flex items-center gap-4 relative">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-accent-indigo flex items-center justify-center text-white shadow-xl shadow-primary/30 animate-float">
            <ShieldCheckIcon class="w-7 h-7" />
          </div>
          <div class="flex flex-col">
            <span class="text-xl font-black tracking-tighter text-slate-800 dark:text-white uppercase">E-Office</span>
            <span class="text-[10px] font-bold text-primary tracking-widest uppercase opacity-70">Digital Solutions</span>
          </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto relative scrollbar-hide">
          <div v-for="(group, gIdx) in groupedMenu" :key="gIdx" class="mb-8">
            <p v-if="group.title" class="px-6 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">{{ group.title }}</p>
            <div class="space-y-1">
              <router-link 
                v-for="item in group.items" 
                :key="item.name"
                :to="item.path" 
                class="group flex items-center gap-4 px-6 py-3.5 rounded-2xl transition-all duration-300 relative overflow-hidden"
                :class="[
                  isRouteActive(item.path) 
                    ? 'bg-primary text-white shadow-lg shadow-primary/25 translate-x-1' 
                    : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:translate-x-1'
                ]"
              >
                <!-- Active Indicator Beam -->
                <div v-if="isRouteActive(item.path)" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-full"></div>
                
                <component 
                  :is="item.icon" 
                  class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"
                />
                <span class="font-bold text-sm tracking-tight">{{ item.name }}</span>
              </router-link>
            </div>
          </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-6 border-t border-slate-100/50 dark:border-slate-800/50 space-y-4 relative bg-white/30 dark:bg-slate-900/30 backdrop-blur-sm">
          <button 
            @click="toggleTheme" 
            class="w-full flex items-center justify-between px-5 py-3.5 bg-slate-50/50 dark:bg-slate-800/50 rounded-2xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all group"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors group-hover:bg-primary/10 group-hover:text-primary">
                <SunIcon v-if="isDark" class="w-5 h-5" />
                <MoonIcon v-else class="w-5 h-5" />
              </div>
              <span class="font-bold text-xs uppercase tracking-wider">{{ isDark ? 'Mode Terang' : 'Mode Gelap' }}</span>
            </div>
            <div class="w-10 h-5 bg-slate-300 dark:bg-slate-700 rounded-full relative p-1 shadow-inner">
              <div 
                class="w-3 h-3 bg-white rounded-full transition-all duration-500 shadow-md"
                :class="isDark ? 'translate-x-5' : 'translate-x-0'"
              ></div>
            </div>
          </button>

          <div class="flex items-center gap-4 p-4 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-3xl border border-white dark:border-slate-700/50 shadow-sm">
            <div class="relative">
              <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center font-black shadow-lg shadow-primary/20">
                {{ userInitial }}
              </div>
              <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-success rounded-full border-4 border-white dark:border-slate-800"></div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-[13px] font-black text-slate-800 dark:text-white truncate leading-tight">{{ auth.user?.name || 'Admin' }}</div>
              <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5 truncate">{{ userRole }}</div>
            </div>
            <button @click="handleLogout" class="hover:text-red-500 transition-colors">
              <LogOutIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 relative">
      <!-- Header -->
      <header class="h-24 bg-white/60 dark:bg-background-dark/60 backdrop-blur-xl border-b border-slate-200/40 dark:border-slate-800/40 px-8 sticky top-0 z-30 flex items-center justify-between gap-8">
        <div class="flex items-center flex-1 gap-6">
          <button @click="isSidebarOpen = true" class="p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-soft lg:hidden text-slate-600 hover:text-primary transition-all">
            <MenuIcon class="w-6 h-6" />
          </button>
          
          <div class="hidden md:flex items-center bg-slate-100/50 dark:bg-slate-800/50 rounded-2xl px-6 py-3 w-full max-w-lg group focus-within:bg-white dark:focus-within:bg-slate-800 focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-500">
            <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
            <input 
              type="text" 
              placeholder="Cari surat atau dokumen..." 
              class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full text-slate-700 dark:text-slate-200 placeholder-slate-400 font-medium"
            />
            <kbd class="hidden sm:flex px-2 py-1 bg-white dark:bg-slate-700 rounded-lg text-[10px] font-black text-slate-400 shadow-sm">CTRL + K</kbd>
          </div>
        </div>

        <div class="flex items-center gap-5">
          <button class="relative p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-soft text-slate-500 hover:text-primary transition-all group overflow-hidden">
            <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/5 transition-colors"></div>
            <BellIcon class="w-5 h-5 relative" />
            <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 ring-4 ring-white dark:ring-slate-800 rounded-full animate-pulse"></span>
          </button>
          
          <div class="w-px h-8 bg-slate-200 dark:bg-slate-800 mx-1 hidden sm:block"></div>
          
          <div class="relative">
            <div 
              @click="isProfileOpen = !isProfileOpen"
              class="flex items-center gap-4 bg-white/50 dark:bg-slate-800/50 p-1.5 pr-5 rounded-2xl border border-white dark:border-slate-700/50 hover:shadow-xl transition-all duration-500 cursor-pointer group active:scale-95 z-50 relative"
            >
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-success to-emerald-600 flex items-center justify-center text-white font-black shadow-lg shadow-success/20 group-hover:rotate-6 transition-transform">
                {{ userInitial }}
              </div>
              <div class="hidden lg:block">
                <div class="text-sm font-black text-slate-800 dark:text-white capitalize leading-none">{{ auth.user?.name }}</div>
                <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mt-1.5 opacity-60">{{ userRole }}</div>
              </div>
            </div>

            <!-- Profile Dropdown Menu -->
            <transition name="dropdown">
              <div 
                v-if="isProfileOpen" 
                class="absolute right-0 top-full mt-4 w-64 bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden z-[60]"
              >
                <div class="p-6 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                   <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Akun Saya</div>
                   <div class="text-sm font-black text-slate-800 dark:text-white truncate">{{ auth.user?.email }}</div>
                </div>
                <div class="p-4 space-y-1">
                  <router-link to="/setting" @click="isProfileOpen = false" class="flex items-center gap-4 px-5 py-3.5 rounded-2xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all group">
                    <UsersIcon class="w-5 h-5 opacity-40 group-hover:opacity-100 transition-opacity" />
                    <span class="text-xs font-black uppercase tracking-widest">Lihat Profil</span>
                  </router-link>
                  <button @click="handleLogout" class="w-full flex items-center gap-4 px-5 py-3.5 rounded-2xl text-slate-600 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-500 transition-all group">
                    <LogOutIcon class="w-5 h-5 opacity-40 group-hover:opacity-100 transition-opacity" />
                    <span class="text-xs font-black uppercase tracking-widest">Logout</span>
                  </button>
                </div>
              </div>
            </transition>

            <!-- Click Outside Overlay -->
            <div v-if="isProfileOpen" @click="isProfileOpen = false" class="fixed inset-0 z-40 bg-transparent"></div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-8 lg:p-12 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
          <!-- Premium Breadcrumb -->
          <transition name="slide-up" appear>
            <div class="mb-12">
              <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">
                <span>Dashboard</span>
                <ChevronRightIcon class="w-3 h-3" />
                <span class="text-primary">{{ currentPageTitle }}</span>
              </div>
              <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight capitalize leading-none">
                {{ currentPageTitle }}
              </h1>
              <p class="text-slate-500 dark:text-slate-400 mt-4 text-sm font-medium tracking-tight bg-slate-100 dark:bg-slate-800 inline-block px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">Manajemen sistem tata kelola persuratan digital instansi.</p>
            </div>
          </transition>
          
          <router-view v-slot="{ Component }">
            <transition name="page-fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import { useRouter, useRoute } from 'vue-router';
import {
  LayoutDashboardIcon,
  InboxIcon,
  SendIcon,
  SettingsIcon,
  ShieldCheckIcon,
  BellIcon,
  SearchIcon,
  MenuIcon,
  SunIcon,
  MoonIcon,
  LogOutIcon,
  ChevronRightIcon,
  UsersIcon,
  FileSearchIcon,
  ArchiveIcon
} from 'lucide-vue-next';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const isSidebarOpen = ref(false);
const isProfileOpen = ref(false);
const isDark = ref(localStorage.getItem('theme') === 'dark');

const groupedMenu = [
  {
    title: 'Main Menu',
    items: [
      { name: 'Dashboard', path: '/', icon: LayoutDashboardIcon },
    ]
  },
  {
    title: 'Layanan Surat',
    items: [
      { name: 'Surat Masuk', path: '/surat-masuk', icon: InboxIcon },
      { name: 'Surat Keluar', path: '/surat-keluar', icon: SendIcon },
      { name: 'Disposisi', path: '/disposisi', icon: FileSearchIcon },
    ]
  },
  {
    title: 'Management',
    items: [
      { name: 'Arsip Digital', path: '/arsip', icon: ArchiveIcon },
      { name: 'Pengguna', path: '/users', icon: UsersIcon },
      { name: 'Settings', path: '/setting', icon: SettingsIcon },
    ]
  }
];

const userInitial = computed(() => auth.user?.name?.charAt(0).toUpperCase() || 'A');
const userRole = computed(() => auth.user?.roles?.[0]?.name || 'Administrator');
const currentPageTitle = computed(() => {
  if (route.path === '/') return 'Overview Statistics';
  return route.name?.replace('-', ' ') || 'Dashboard';
});

const isRouteActive = (path) => {
  if (path === '/' && route.path === '/') return true;
  if (path !== '/' && route.path.startsWith(path)) return true;
  return false;
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
  updateTheme();
};

const updateTheme = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/login');
};

onMounted(async () => {
  updateTheme();
  if (!auth.user) {
    await auth.fetchUser();
  }
});
</script>

<style scoped>
.page-fade-enter-active,
.page-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.page-fade-enter-from,
.page-fade-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-up-enter-active { transition: all 0.8s cubic-bezier(0.165, 0.84, 0.44, 1); }
.slide-up-enter-from { opacity: 0; transform: translateY(30px); }

.dropdown-enter-active, .dropdown-leave-active { 
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
}
.dropdown-enter-from, .dropdown-leave-to { 
  opacity: 0; 
  transform: translateY(-10px) scale(0.95); 
}

/* Hide scrollbar */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Dashboard Glassmorphism Card Style available globally via style.css layer */
</style>
