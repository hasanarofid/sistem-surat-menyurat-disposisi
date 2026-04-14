<template>
  <div class="space-y-10 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center gap-6">
        <router-link to="/disposisi" class="p-4 bg-white dark:bg-slate-900 rounded-2xl shadow-premium text-slate-400 hover:text-primary transition-all">
          <ArrowLeftIcon class="w-6 h-6" />
        </router-link>
        <div>
          <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Alur Disposisi</h1>
          <p class="text-slate-500 dark:text-slate-500 text-sm font-medium mt-1">Lacak dan delegasikan instruksi surat masuk.</p>
        </div>
      </div>
      <button @click="showAddModal = true" class="btn-primary group flex items-center gap-3">
        <PlusIcon class="w-5 h-5 transition-transform group-hover:rotate-90" />
        <span>Buat Disposisi</span>
      </button>
    </div>

    <div v-if="surat" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      <!-- Surat Info Sidebar -->
      <div class="lg:col-span-4 space-y-6">
        <div class="card relative overflow-hidden border-none shadow-premium bg-gradient-to-br from-primary to-indigo-600 text-white">
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
          
          <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
              <FileIcon class="w-6 h-6" />
            </div>
            <div>
              <div class="text-[10px] font-black uppercase tracking-widest opacity-60 leading-none">Nomor Surat</div>
              <div class="text-lg font-black mt-1">{{ surat.nomor_surat }}</div>
            </div>
          </div>

          <div class="space-y-6 relative">
            <div>
              <div class="text-[10px] font-black uppercase tracking-widest opacity-60">Asal Surat</div>
              <div class="text-sm font-bold mt-1">{{ surat.asal_surat }}</div>
            </div>
            <div>
              <div class="text-[10px] font-black uppercase tracking-widest opacity-60">Perihal</div>
              <div class="text-sm font-medium mt-1 leading-relaxed">{{ surat.perihal }}</div>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-white/10">
               <div class="text-[10px] font-black uppercase tracking-widest opacity-60">Tgl Terima</div>
               <div class="text-xs font-bold">{{ formatDate(surat.tanggal_terima) }}</div>
            </div>
          </div>
        </div>

        <!-- Attachment Card (Functional) -->
        <div v-if="surat.file_path" class="card bg-white dark:bg-slate-900 border-none shadow-soft flex items-center justify-between p-6">
           <div class="flex items-center gap-4">
             <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
               <DownloadIcon class="w-5 h-5" />
             </div>
             <span class="text-xs font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest">Unduh Softcopy</span>
           </div>
           <a :href="surat.file_path" target="_blank" class="px-6 py-2 bg-primary/10 rounded-xl text-primary font-black text-xs hover:bg-primary hover:text-white transition-all uppercase tracking-widest text-center">
             Buka / Unduh
           </a>
        </div>
        <div v-else class="card bg-slate-50 dark:bg-slate-800/20 border border-dashed border-slate-200 dark:border-slate-800 p-6 text-center">
           <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tidak ada lampiran berkas</p>
        </div>
      </div>

      <!-- Disposisi History Timeline -->
      <div class="lg:col-span-8">
        <div class="card min-h-[500px] border-none shadow-premium bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl">
           <div class="flex items-center justify-between mb-12">
             <h3 class="text-xl font-black text-slate-800 dark:text-white">Timeline Instruksi</h3>
             <span class="px-4 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-black uppercase tracking-widest text-slate-500">{{ disposisi.length }} Langkah</span>
           </div>

           <div class="space-y-0 relative">
             <!-- Vertical Timeline Line -->
             <div class="absolute left-6 top-0 bottom-0 w-px bg-slate-200 dark:bg-slate-800"></div>

             <div v-for="(item, idx) in disposisi" :key="item.id" class="relative pl-16 pb-12 group last:pb-0">
                <!-- Timeline Dot -->
                <div class="absolute left-[20px] top-2 w-2.5 h-2.5 rounded-full bg-primary border-4 border-white dark:border-slate-900 shadow-glow transition-transform group-hover:scale-150 z-10"></div>
                
                <div class="flex flex-col gap-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                       <span class="text-xs font-black text-primary uppercase tracking-tight">{{ item.pengirim?.name }}</span>
                       <ArrowRightIcon class="w-3 h-3 text-slate-300" />
                       <span class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ item.penerima?.name }}</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ formatDate(item.created_at) }}</span>
                  </div>

                  <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft group-hover:shadow-glow transition-all duration-500">
                    <div class="flex items-start gap-4">
                      <div class="mt-1">
                        <QuoteIcon class="w-5 h-5 text-slate-200 rotate-180" />
                      </div>
                      <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic leading-relaxed">"{{ item.instruksi }}"</p>
                    </div>
                    <div v-if="item.catatan" class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 text-[11px] font-bold text-slate-400">
                      PS: {{ item.catatan }}
                    </div>
                  </div>

                  <div class="flex items-center justify-end gap-3">
                    <button v-if="!item.is_signed && item.penerima_id === currentUser?.id" @click="handleSign(item.id)" class="px-3 py-1 bg-primary/10 rounded-xl text-primary text-[9px] font-black uppercase tracking-widest border border-primary/20 hover:bg-primary hover:text-white transition-all">
                      Tanda Tangani
                    </button>
                    <div v-if="item.is_signed && item.signature_path" class="flex flex-col items-end">
                       <img :src="item.signature_path" class="h-10 w-auto opacity-80 mix-blend-multiply dark:invert" alt="Signature">
                       <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Signed E-Office</span>
                    </div>
                    <span :class="item.status === 'completed' ? 'text-success bg-success/10 border-success/20' : 'text-amber-500 bg-amber-500/10 border-amber-500/20'" class="px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest border">
                      {{ item.status }}
                    </span>
                  </div>
                </div>
             </div>

             <div v-if="disposisi.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
               <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-6">
                 <WindIcon class="w-10 h-10 text-slate-200" />
               </div>
               <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Belum ada alur disposisi</p>
               <button @click="showAddModal = true" class="mt-4 text-primary font-black text-xs hover:underline uppercase tracking-widest">Mulai delegasi pertama</button>
             </div>
           </div>
        </div>
      </div>
    </div>

    <!-- Modal Form with SearchableSelect -->
    <Modal :show="showAddModal" title="Buat Instruksi Disposisi" @close="showAddModal = false" @submit="handleSubmit">
      <form class="space-y-8">
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Penerima Instruksi</label>
          <SearchableSelect 
            v-model="form.penerima_id" 
            :options="users" 
            placeholder="Ketik nama pelaksana..." 
          />
        </div>
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Instruksi Utama</label>
          <textarea v-model="form.instruksi" class="input-field h-32 py-4" placeholder="Apa yang harus dilakukan?"></textarea>
        </div>
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan Tambahan (Opsional)</label>
          <input v-model="form.catatan" type="text" class="input-field h-12" placeholder="Catatan kecil atau deadline...">
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { 
  ArrowLeftIcon, PlusIcon, FileIcon, DownloadIcon, 
  ArrowRightIcon, QuoteIcon, WindIcon, PenToolIcon 
} from 'lucide-vue-next';
import api from '@/api/axios';
import Modal from '@/components/Modal.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { useAuthStore } from '@/store/auth';

const route = useRoute();
const authStore = useAuthStore();
const currentUser = computed(() => authStore.user);
const surat = ref(null);
const disposisi = ref([]);
const users = ref([]);
const showAddModal = ref(false);

const form = reactive({
    surat_masuk_id: route.params.id,
    penerima_id: '',
    instruksi: '',
    catatan: '',
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchData = async () => {
    try {
        const suratRes = await api.get(`/surat-masuk/${route.params.id}`);
        surat.value = suratRes.data;
        
        const disposisiRes = await api.get(`/disposisi/${route.params.id}`);
        disposisi.value = disposisiRes.data;

        // Fetch users for SearchableSelect
        const usersRes = await api.get('/users'); 
        users.value = usersRes.data.data;
    } catch (err) {
        console.error('Fetch error', err);
    }
};

const handleSubmit = async () => {
    try {
        await api.post('/disposisi', form);
        showAddModal.value = false;
        fetchData();
        Object.assign(form, { penerima_id: '', instruksi: '', catatan: '' });
    } catch (err) {
        alert('Gagal membuat disposisi');
    }
};

const handleSign = async (id) => {
    if (confirm('Tanda tangani instruksi ini?')) {
        try {
            await api.post(`/disposisi/${id}/sign`);
            fetchData();
        } catch (err) {
            alert('Gagal menandatangani. Pastikan tanda tangan sudah diatur di profil.');
        }
    }
};

onMounted(fetchData);
</script>
