<template>
  <div class="space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Arsip Digital</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Penyimpanan permanen dokumen persuratan yang telah selesai diproses.</p>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl shadow-inner">
          <button @click="changeType('masuk')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300" :class="activeType === 'masuk' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm scale-105' : 'text-slate-500 hover:text-primary'">Surat Masuk</button>
          <button @click="changeType('keluar')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300" :class="activeType === 'keluar' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm scale-105' : 'text-slate-500 hover:text-primary'">Surat Keluar</button>
        </div>
      </div>
    </div>

    <!-- Quick Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div v-for="tag in filterTags" :key="tag" class="card p-5 flex items-center gap-4 hover:translate-y-[-4px] cursor-pointer group active:scale-95 transition-all">
        <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-all">
          <HashIcon class="w-5 h-5 transition-transform group-hover:rotate-12" />
        </div>
        <div>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Tahun Arsip</span>
          <div class="text-sm font-black text-slate-700 dark:text-white mt-1">{{ tag }}</div>
        </div>
      </div>
    </div>

    <div class="card p-0 overflow-hidden border-none shadow-premium bg-white dark:bg-slate-900 relative">
      <div v-if="loading" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] z-10 flex items-center justify-center">
        <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center bg-slate-100/50 dark:bg-slate-800/50 rounded-2xl px-6 py-3 w-full max-w-xl group focus-within:bg-white dark:focus-within:bg-slate-800 focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-500">
          <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
          <input 
            v-model="search" 
            @input="handleSearch"
            type="text" 
            placeholder="Cari nomor surat atau perihal..." 
            class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium" 
          />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50 dark:bg-slate-800/30">
              <th class="px-8 py-5">Identitas Arsip</th>
              <th class="px-8 py-5">{{ activeType === 'masuk' ? 'Asal' : 'Tujuan' }} Surat</th>
              <th class="px-8 py-5">Tgl Arsip</th>
              <th class="px-8 py-5">Lokasi Simpan</th>
              <th class="px-8 py-5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30">
            <tr v-for="item in items" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300">
              <td class="px-8 py-6">
                <div class="flex items-center gap-5">
                   <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:scale-110 group-hover:text-primary transition-all duration-500 shadow-inner">
                     <FileTextIcon class="w-6 h-6" />
                   </div>
                   <div>
                     <div class="text-[13px] font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors cursor-pointer">{{ item.nomor_surat }}</div>
                     <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-wider truncate max-w-[200px]">{{ item.perihal }}</div>
                   </div>
                </div>
              </td>
              <td class="px-8 py-6 text-[13px] font-black text-slate-600 dark:text-slate-300">
                {{ activeType === 'masuk' ? item.asal_surat : item.tujuan_surat }}
              </td>
              <td class="px-8 py-6">
                <div class="text-[13px] font-black text-slate-600 dark:text-slate-300">{{ formatDate(item.tanggal_terima || item.tanggal_surat) }}</div>
                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Selesai Diproses</div>
              </td>
              <td class="px-8 py-6">
                <span class="inline-flex px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/50 dark:border-slate-700/50">
                   RAK-{{ item.id % 20 + 1 }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary hover:bg-white dark:hover:bg-slate-700 transition-all shadow-sm">
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-success hover:bg-white dark:hover:bg-slate-700 transition-all shadow-sm">
                    <DownloadIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="items.length === 0 && !loading">
              <td colspan="5" class="px-8 py-16 text-center text-slate-400 italic font-medium">Belum ada dokumen yang diarsipkan di kategori ini.</td>
            </tr>
          </tbody>
        </table>
      </div>

       <!-- Pagination -->
       <div v-if="pagination.last_page > 1" class="p-8 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
         <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
           {{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }} arsip
         </span>
         <div class="flex items-center gap-2">
           <button @click="fetchData(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 disabled:opacity-30 hover:text-primary transition-all">
             <ChevronLeftIcon class="w-5 h-5" />
           </button>
           <button @click="fetchData(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 disabled:opacity-30 hover:text-primary transition-all">
             <ChevronRightIcon class="w-5 h-5" />
           </button>
         </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { 
  SearchIcon, FileTextIcon, HashIcon, EyeIcon, DownloadIcon, 
  ChevronLeftIcon, ChevronRightIcon 
} from 'lucide-vue-next';
import api from '@/api/axios';
import { debounce } from '@/utils/debounce';

const activeType = ref('masuk');
const items = ref([]);
const loading = ref(false);
const search = ref('');
const pagination = ref({});
const filterTags = ['2026', '2025', '2024', 'Tertua'];

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchData = async (page = 1) => {
    loading.value = true;
    const endpoint = activeType.value === 'masuk' ? '/surat-masuk' : '/surat-keluar';
    try {
        const { data } = await api.get(endpoint, {
            params: { 
                page, 
                search: search.value, 
                status: activeType.value === 'masuk' ? 'archived' : 'sent' 
            }
        });
        items.value = data.data;
        pagination.value = data;
    } catch (e) {
        console.error('Fetch arsip error', e);
    } finally {
        loading.value = false;
    }
};

const handleSearch = debounce(() => {
    fetchData(1);
}, 500);

const changeType = (type) => {
    activeType.value = type;
    fetchData(1);
};

onMounted(() => fetchData(1));
</script>
