<template>
  <div class="space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Daftar Disposisi</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Monitoring alur instruksi dan disposisi surat secara real-time.</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="flex items-center bg-white dark:bg-slate-900 rounded-2xl px-6 py-3 w-full max-w-xl shadow-soft group focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-300">
        <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
        <input 
          v-model="search" 
          @input="handleSearch"
          type="text" 
          placeholder="Cari nomor surat atau instruksi..." 
          class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium" 
        />
      </div>
    </div>

    <div class="card p-0 overflow-hidden border-none shadow-premium bg-white dark:bg-slate-900 relative">
      <div v-if="loading" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] z-10 flex items-center justify-center">
        <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50 dark:bg-slate-800/30">
              <th class="px-8 py-5">Surat Terkait</th>
              <th class="px-8 py-5">Alur Instruksi</th>
              <th class="px-8 py-5">Tgl Disposisi</th>
              <th class="px-8 py-5">Instruksi</th>
              <th class="px-8 py-5">Status</th>
              <th class="px-8 py-5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30">
            <tr v-for="item in items" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300">
              <td class="px-8 py-6">
                <div class="flex flex-col">
                  <span class="text-[13px] font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors cursor-pointer">{{ item.surat_masuk?.nomor_surat }}</span>
                  <span class="text-[10px] font-bold text-slate-400 uppercase mt-1 truncate max-w-[150px]">{{ item.surat_masuk?.perihal }}</span>
                </div>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center gap-3">
                  <div class="flex flex-col items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ item.pengirim?.name?.split(' ')[0] }}</span>
                    <ArrowDownIcon class="w-3 h-3 text-slate-300 my-0.5" />
                    <span class="text-[10px] font-black text-primary uppercase leading-none">{{ item.penerima?.name?.split(' ')[0] }}</span>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6 text-[13px] font-bold text-slate-600 dark:text-slate-400">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="px-8 py-6">
                <p class="text-sm text-slate-600 dark:text-slate-400 italic font-medium max-w-xs truncate" :title="item.instruksi">"{{ item.instruksi }}"</p>
              </td>
              <td class="px-8 py-6">
                <span :class="statusClass(item.status)" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <router-link :to="`/disposisi/${item.surat_masuk_id}`" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary hover:bg-white dark:hover:bg-slate-700 hover:shadow-md transition-all inline-block">
                  <EyeIcon class="w-5 h-5" />
                </router-link>
              </td>
            </tr>
            <tr v-if="items.length === 0 && !loading">
              <td colspan="6" class="px-8 py-16 text-center text-slate-400 italic font-medium">Belum ada data disposisi yang tercatat.</td>
            </tr>
          </tbody>
        </table>
      </div>

       <!-- Pagination -->
       <div v-if="pagination.last_page > 1" class="p-8 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
         <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
           Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
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
import { SearchIcon, EyeIcon, ArrowDownIcon, ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';
import api from '@/api/axios';
import { debounce } from '@/utils/debounce';

const items = ref([]);
const loading = ref(false);
const search = ref('');
const pagination = ref({});

const statusClass = (status) => {
    switch (status) {
        case 'completed': return 'text-green-500 bg-green-50 dark:bg-green-900/30';
        default: return 'text-amber-500 bg-amber-50 dark:bg-amber-900/30';
    }
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchData = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/disposisi-all', {
            params: { page, search: search.value }
        });
        items.value = data.data;
        pagination.value = data;
    } catch (e) {
        console.error('Fetch error', e);
    } finally {
        loading.value = false;
    }
};

const handleSearch = debounce(() => {
    fetchData(1);
}, 500);

onMounted(() => fetchData(1));
</script>
