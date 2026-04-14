<template>
  <div class="space-y-8 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Surat Masuk</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Manajemen pencatatan surat masuk dan delegasi instruksi.</p>
      </div>
      <button @click="openModal()" class="btn-primary group flex items-center gap-3">
        <PlusIcon class="w-5 h-5 transition-transform group-hover:rotate-90" />
        <span>Tambah Surat</span>
      </button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="flex items-center bg-white dark:bg-slate-900 rounded-2xl px-6 py-3 w-full max-w-xl shadow-soft group focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-300">
        <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
        <input 
          v-model="search" 
          @input="handleSearch"
          type="text" 
          placeholder="Cari nomor surat atau perihal..." 
          class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium"
        />
      </div>
      <div class="flex items-center gap-2">
         <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-2">Urutkan:</span>
         <select v-model="perPage" @change="fetchItems(1)" class="bg-white dark:bg-slate-900 border-none rounded-xl text-xs font-black p-2 shadow-soft focus:ring-primary/20">
           <option :value="10">10 Baris</option>
           <option :value="25">25 Baris</option>
           <option :value="50">50 Baris</option>
         </select>
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
              <th class="px-8 py-5">Identitas Surat</th>
              <th class="px-8 py-5">Asal Surat</th>
              <th class="px-8 py-5">Tgl Terima</th>
              <th class="px-8 py-5 text-center">Status</th>
              <th class="px-8 py-5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30">
            <tr v-for="item in items" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all duration-300">
              <td class="px-8 py-6">
                <div class="flex items-center gap-5">
                  <div class="w-12 h-12 rounded-2xl bg-primary/5 text-primary flex items-center justify-center group-hover:scale-110 group-hover:bg-primary/10 transition-all duration-500">
                     <InboxIcon class="w-6 h-6" />
                  </div>
                  <div>
                    <div class="text-[13px] font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors cursor-pointer">{{ item.nomor_surat }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-wider truncate max-w-[200px]">{{ item.perihal }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <span class="text-[13px] font-black text-slate-600 dark:text-slate-300">{{ item.asal_surat }}</span>
              </td>
              <td class="px-8 py-6">
                <div class="text-[13px] font-black text-slate-600 dark:text-slate-300">{{ formatDate(item.tanggal_terima) }}</div>
                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Surat: {{ formatDate(item.tanggal_surat) }}</div>
              </td>
              <td class="px-8 py-6 text-center">
                <span :class="statusClass(item.status)" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="handleDisposisi(item)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-blue-500 transition-all shadow-sm" title="Disposisi">
                    <SendIcon class="w-4 h-4" />
                  </button>
                  <button @click="openModal(item)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary transition-all shadow-sm" title="Edit">
                    <EditIcon class="w-4 h-4" />
                  </button>
                  <button @click="handleDelete(item.id)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-red-500 transition-all shadow-sm" title="Hapus">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-8 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
         <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
           Menampilkan {{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }} surat
         </span>
         <div class="flex items-center gap-2">
           <button 
            @click="fetchItems(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 disabled:opacity-30 hover:text-primary transition-all"
           >
             <ChevronLeftIcon class="w-5 h-5" />
           </button>
           <div class="flex items-center gap-1">
              <button 
                v-for="p in visiblePages" :key="p"
                @click="fetchItems(p)"
                class="w-10 h-10 rounded-xl text-xs font-black transition-all"
                :class="pagination.current_page === p ? 'bg-primary text-white shadow-glow' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
              >
                {{ p }}
              </button>
           </div>
           <button 
            @click="fetchItems(pagination.current_page + 1)" 
            :disabled="pagination.current_page === pagination.last_page"
            class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 disabled:opacity-30 hover:text-primary transition-all"
           >
             <ChevronRightIcon class="w-5 h-5" />
           </button>
         </div>
      </div>
    </div>

    <!-- Professional Modal -->
    <Modal :show="showAddModal" :title="editMode ? 'Edit Surat Masuk' : 'Tambah Surat Baru'" @close="closeModal" @submit="handleSubmit">
      <form class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Surat</label>
            <input v-model="form.nomor_surat" type="text" class="input-field" placeholder="SM/2026/0001">
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori / Status</label>
            <select v-model="form.status" class="input-field">
              <option value="pending">PENDING</option>
              <option value="processed">PROCESSED</option>
              <option value="archived">ARCHIVED</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Surat</label>
            <input v-model="form.tanggal_surat" type="date" class="input-field">
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Terima</label>
            <input v-model="form.tanggal_terima" type="date" class="input-field">
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Asal Surat / Instansi</label>
          <input v-model="form.asal_surat" type="text" class="input-field" placeholder="PT Contoh Jaya Abadi">
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Perihal / Isi Ringkas</label>
          <textarea v-model="form.perihal" class="input-field h-24" placeholder="Keterangan perihal surat..."></textarea>
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">File Dokumen</label>
          <div class="flex items-center justify-center p-8 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all cursor-pointer relative overflow-hidden group">
             <input type="file" @change="handleFileUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
             <div class="flex flex-col items-center pointer-events-none">
               <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                 <CloudUploadIcon class="w-6 h-6" />
               </div>
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ form.file ? form.file.name : 'Klik atau tarik file ke sini' }}</p>
               <p class="text-[9px] text-slate-400 mt-2 italic">PDF, JPG, atau PNG (Max 2MB)</p>
             </div>
          </div>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { 
  InboxIcon, 
  PlusIcon, 
  SearchIcon, 
  EditIcon, 
  TrashIcon, 
  SendIcon, 
  ChevronLeftIcon, 
  ChevronRightIcon,
  CloudUploadIcon
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import { debounce } from '@/utils/debounce';
import api from '@/api/axios';

const router = useRouter();

const items = ref([]);
const loading = ref(false);
const showAddModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const search = ref('');
const perPage = ref(10);
const pagination = ref({});

const form = reactive({
    nomor_surat: '',
    tanggal_surat: '',
    tanggal_terima: '',
    asal_surat: '',
    perihal: '',
    status: 'pending',
    file: null,
});

const statusClass = (status) => {
    switch (status) {
        case 'processed': return 'text-blue-500 bg-blue-50 dark:bg-blue-900/30';
        case 'archived': return 'text-green-500 bg-green-50 dark:bg-green-900/30';
        default: return 'text-amber-500 bg-amber-50 dark:bg-amber-900/30';
    }
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchItems = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/surat-masuk', {
            params: {
                page,
                search: search.value,
                per_page: perPage.value
            }
        });
        items.value = data.data;
        pagination.value = data;
    } catch (err) {
        console.error('Fetch error', err);
    } finally {
        loading.value = false;
    }
};

const handleSearch = debounce(() => {
    fetchItems(1);
}, 500);

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const handleSubmit = async () => {
    const formData = new FormData();
    formData.append('nomor_surat', form.nomor_surat);
    formData.append('tanggal_surat', form.tanggal_surat);
    formData.append('tanggal_terima', form.tanggal_terima);
    formData.append('asal_surat', form.asal_surat);
    formData.append('perihal', form.perihal);
    formData.append('status', form.status);
    if (form.file) formData.append('file', form.file);

    try {
        if (editMode.value) {
            await api.post(`/surat-masuk/${selectedId.value}?_method=PUT`, formData);
        } else {
            await api.post('/surat-masuk', formData);
        }
        closeModal();
        fetchItems(pagination.value.current_page);
    } catch (err) {
        alert('Gagal menyimpan data');
    }
};

const openModal = (item = null) => {
    if (item) {
        editMode.value = true;
        selectedId.value = item.id;
        Object.assign(form, {
            nomor_surat: item.nomor_surat,
            tanggal_surat: item.tanggal_surat,
            tanggal_terima: item.tanggal_terima,
            asal_surat: item.asal_surat,
            perihal: item.perihal,
            status: item.status,
            file: null
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        Object.assign(form, {
            nomor_surat: '',
            tanggal_surat: '',
            tanggal_terima: '',
            asal_surat: '',
            perihal: '',
            status: 'pending',
            file: null,
        });
    }
    showAddModal.value = true;
};

const handleDelete = async (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus surat ini?')) {
        try {
            await api.delete(`/surat-masuk/${id}`);
            fetchItems(pagination.value.current_page);
        } catch (e) {
            alert('Gagal menghapus data');
        }
    }
};

const handleDisposisi = (item) => {
    router.push(`/disposisi/${item.id}`);
};

const closeModal = () => {
    showAddModal.value = false;
};

const visiblePages = computed(() => {
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const pages = [];
  
  let start = Math.max(1, current - 2);
  let end = Math.min(last, current + 2);
  
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

onMounted(() => fetchItems(1));
</script>
