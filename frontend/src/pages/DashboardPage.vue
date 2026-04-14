<template>
  <div class="space-y-12 pb-24">
    <!-- Premium Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="(stat, idx) in statCards" :key="idx" 
        class="group card relative overflow-hidden flex flex-col justify-between hover:translate-y-[-8px] transition-all duration-500"
      >
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
        
        <div class="flex items-center justify-between mb-4">
          <div :class="`w-14 h-14 rounded-2xl flex items-center justify-center bg-${stat.color}-50 dark:bg-${stat.color}-900/20 text-${stat.color}-600 dark:text-${stat.color}-400 shadow-inner group-hover:scale-110 transition-all duration-500`">
            <component :is="stat.icon" class="w-7 h-7" />
          </div>
          <div class="flex flex-col items-end">
             <div class="flex items-center text-success text-[10px] font-bold">
              <TrendingUpIcon class="w-3 h-3 mr-1" />
              <span>Dinamis</span>
            </div>
          </div>
        </div>
        
        <div>
          <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter">{{ stat.value }}</h3>
          <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-2">{{ stat.title }}</p>
        </div>
      </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      <!-- Pie Chart 1: Surat Masuk Status -->
      <div class="lg:col-span-4 card relative overflow-hidden border-none shadow-premium bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-950">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Status Surat Masuk</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Real Data Seeder</p>
          </div>
        </div>
        <div class="h-80 flex items-center justify-center relative transition-all duration-700 hover:scale-105">
          <Pie v-if="categoryData" :data="categoryData" :options="chartOptions" />
        </div>
        <div class="mt-10 space-y-4">
          <div v-for="(val, label) in categoryLegend" :key="label" class="flex items-center justify-between p-4 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all">
            <div class="flex items-center gap-4">
              <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: val.color }"></div>
              <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ label }}</span>
            </div>
            <span class="text-xs font-black text-slate-800 dark:text-white">{{ val.value }} Surat</span>
          </div>
        </div>
      </div>

      <!-- Pie Chart 2: Gender Stats -->
      <div class="lg:col-span-4 card relative overflow-hidden border-none shadow-premium bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-950">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Distribusi Pengguna</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Berdasarkan Gender</p>
          </div>
        </div>
        <div class="h-80 flex items-center justify-center relative transition-all duration-700 hover:scale-105">
          <Pie v-if="genderData" :data="genderData" :options="chartOptions" />
        </div>
        <div class="mt-10 space-y-4">
           <div class="flex items-center justify-between p-4 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
             <div class="flex items-center gap-4">
               <div class="w-3 h-3 rounded-full bg-blue-500"></div>
               <span class="text-xs font-black text-slate-500 uppercase">Laki-Laki</span>
             </div>
             <span class="text-xs font-black text-slate-800 dark:text-white">45%</span>
           </div>
           <div class="flex items-center justify-between p-4 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
             <div class="flex items-center gap-4">
               <div class="w-3 h-3 rounded-full bg-rose-400"></div>
               <span class="text-xs font-black text-slate-500 uppercase">Perempuan</span>
             </div>
             <span class="text-xs font-black text-slate-800 dark:text-white">55%</span>
           </div>
        </div>
      </div>

      <!-- Quick Info -->
      <div class="lg:col-span-4 card bg-primary text-white border-none shadow-premium relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl text-white"></div>
        <h3 class="text-xl font-black mb-6 relative">Summary Sistem</h3>
        <div class="space-y-6 relative">
          <div class="p-6 bg-white/10 rounded-3xl border border-white/20">
             <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Disposisi Aktif</span>
             <div class="text-4xl font-black mt-2">{{ stats.total_disposisi || 0 }}</div>
          </div>
          <div class="p-6 bg-white/10 rounded-3xl border border-white/20">
             <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Pengguna Terdaftar</span>
             <div class="text-4xl font-black mt-2">{{ stats.total_users || 0 }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { 
  InboxIcon, 
  SendIcon, 
  FileTextIcon, 
  UsersIcon, 
  TrendingUpIcon,
} from 'lucide-vue-next';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'vue-chartjs';
import api from '@/api/axios';

ChartJS.register(ArcElement, Tooltip, Legend);

const stats = ref({});
const loading = ref(true);

const statCards = computed(() => [
  { title: 'Surat Masuk', value: stats.value.total_surat_masuk || 0, icon: InboxIcon, color: 'primary' },
  { title: 'Surat Keluar', value: stats.value.total_surat_keluar || 0, icon: SendIcon, color: 'blue' },
  { title: 'Disposisi Baru', value: stats.value.total_disposisi || 0, icon: FileTextIcon, color: 'success' },
  { title: 'Total Pengguna', value: stats.value.total_users || 0, icon: UsersIcon, color: 'rose' },
]);

const categoryData = computed(() => {
  if (!stats.value.surat_masuk_status) return null;
  return {
    labels: ['Pending', 'Processed', 'Archived'],
    datasets: [{
      backgroundColor: ['#f59e0b', '#6366f1', '#10b981'],
      data: [
        stats.value.surat_masuk_status.pending,
        stats.value.surat_masuk_status.processed,
        stats.value.surat_masuk_status.archived
      ],
      borderWidth: 0,
      hoverOffset: 20
    }]
  };
});

const genderData = computed(() => ({
  labels: ['L', 'P'],
  datasets: [{
    backgroundColor: ['#3b82f6', '#fb7185'],
    data: [45, 55],
    borderWidth: 0,
    hoverOffset: 20
  }]
}));

const categoryLegend = computed(() => {
  if (!stats.value.surat_masuk_status) return {};
  return {
    'Pending': { value: stats.value.surat_masuk_status.pending, color: '#f59e0b' },
    'Processed': { value: stats.value.surat_masuk_status.processed, color: '#6366f1' },
    'Archived': { value: stats.value.surat_masuk_status.archived, color: '#10b981' },
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  cutout: '70%',
};

const fetchStats = async () => {
    try {
        const { data } = await api.get('/dashboard/stats');
        stats.value = data;
    } catch (e) {
        console.error('Failed to fetch stats', e);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchStats);
</script>

<style scoped>
.card { animation: slideUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; opacity: 0; }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>
