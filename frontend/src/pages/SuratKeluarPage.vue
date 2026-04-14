<template>
  <div class="space-y-8 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Surat Keluar</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Manajemen pencatatan and pengiriman surat keluar resmi.</p>
      </div>
      <button @click="openModal()" class="btn-primary group flex items-center gap-3">
        <EditIcon class="w-5 h-5 transition-transform group-hover:scale-110" />
        <span>Catat Surat Keluar</span>
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
          placeholder="Cari nomor surat atau tujuan..." 
          class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium"
        />
      </div>
      <div class="flex items-center gap-2">
         <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-2">Tampilkan:</span>
         <select v-model="perPage" @change="fetchItems(1)" class="bg-white dark:bg-slate-900 border-none rounded-xl text-xs font-black p-2 shadow-soft focus:ring-primary/20">
           <option :value="10">10 Baris</option>
           <option :value="25">25 Baris</option>
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
              <th class="px-8 py-5">Tujuan Surat</th>
              <th class="px-8 py-5">Tgl Surat</th>
              <th class="px-8 py-5 text-center">Status</th>
              <th class="px-8 py-5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30">
            <tr v-for="item in items" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all duration-300">
              <td class="px-8 py-6">
                <div class="flex items-center gap-5">
                  <div class="w-12 h-12 rounded-2xl bg-blue-500/5 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-all duration-500 shadow-inner">
                     <SendIcon class="w-6 h-6" />
                  </div>
                  <div>
                    <div class="text-[13px] font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors cursor-pointer">{{ item.nomor_surat }}</div>
                    <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-wider truncate max-w-[200px]">{{ item.perihal }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <span class="text-[13px] font-black text-slate-600 dark:text-slate-300">{{ item.tujuan_surat }}</span>
              </td>
              <td class="px-8 py-6">
                <div class="text-[13px] font-black text-slate-600 dark:text-slate-300">{{ formatDate(item.tanggal_surat) }}</div>
                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest leading-none">Dokumen Terarsip</div>
              </td>
              <td class="px-8 py-6 text-center">
                <span :class="statusClass(item.status)" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2 text-nowrap">
                  <button @click="openModal(item)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary transition-all shadow-sm">
                    <EditIcon class="w-4 h-4" />
                  </button>
                  <button @click="handleDelete(item.id)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-red-500 transition-all shadow-sm">
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
           {{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }} surat keluar
         </span>
         <div class="flex items-center gap-2">
           <button @click="fetchItems(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 disabled:opacity-30 hover:text-primary transition-all">
             <ChevronLeftIcon class="w-5 h-5" />
           </button>
           <button @click="fetchItems(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 disabled:opacity-30 hover:text-primary transition-all">
             <ChevronRightIcon class="w-5 h-5" />
           </button>
         </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Modal :show="showAddModal" :title="editMode ? 'Edit Surat Keluar' : 'Catat Surat Keluar Baru'" @close="closeModal" @submit="handleSubmit">
      <form class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Surat</label>
            <input v-model="form.nomor_surat" type="text" class="input-field" placeholder="SK/2026/0001">
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status Pengiriman</label>
            <select v-model="form.status" class="input-field">
              <option value="draft">DRAFT</option>
              <option value="sent">SENT / TERKIRIM</option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Surat</label>
            <input v-model="form.tanggal_surat" type="date" class="input-field">
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tujuan Surat / Instansi Luar</label>
          <input v-model="form.tujuan_surat" type="text" class="input-field" placeholder="Nama Instansi atau Perorangan">
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Perihal / Isi Ringkas</label>
          <textarea v-model="form.perihal" class="input-field h-24" placeholder="Keterangan perihal surat keluar..."></textarea>
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Unggah Softcopy (PDF/JPG)</label>
          <div class="flex items-center justify-center p-10 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all cursor-pointer relative group">
             <input type="file" @change="handleFileUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
             <div class="flex flex-col items-center pointer-events-none">
               <CloudUploadIcon class="w-10 h-10 text-primary opacity-20 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 mb-4" />
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ form.file ? form.file.name : 'Pilih file dokumen' }}</p>
             </div>
          </div>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { 
  PlusIcon, SearchIcon, EditIcon, TrashIcon, SendIcon, 
  ChevronLeftIcon, ChevronRightIcon, CloudUploadIcon 
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import { debounce } from '@/utils/debounce';

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
    tujuan_surat: '',
    perihal: '',
    status: 'draft',
    file: null,
});

const statusClass = (status) => {
    return status === 'sent' ? 'text-green-500 bg-green-50 dark:bg-green-900/30' : 'text-slate-500 bg-slate-50 dark:bg-slate-900/30';
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchItems = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/surat-keluar', {
            params: { page, search: search.value, per_page: perPage.value }
        });
        items.value = data.data;
        pagination.value = data;
    } catch (err) {} finally { loading.value = false; }
};

const handleSearch = debounce(() => {
    fetchItems(1);
}, 500);

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const openModal = (item = null) => {
    if (item) {
        editMode.value = true;
        selectedId.value = item.id;
        Object.assign(form, {
            nomor_surat: item.nomor_surat,
            tanggal_surat: item.tanggal_surat,
            tujuan_surat: item.tujuan_surat,
            perihal: item.perihal,
            status: item.status,
            file: null
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        Object.assign(form, {
            nomor_surat: '', tanggal_surat: '', tujuan_surat: '', perihal: '', status: 'draft', file: null
        });
    }
    showAddModal.value = true;
};

const handleSubmit = async () => {
    const formData = new FormData();
    for (const key in form) { if (form[key]) formData.append(key, form[key]); }

    try {
        if (editMode.value) {
            await api.post(`/surat-keluar/${selectedId.value}?_method=PUT`, formData);
        } else {
            await api.post('/surat-keluar', formData);
        }
        closeModal();
        fetchItems(pagination.value.current_page);
    } catch (err) { alert('Gagal memproses data'); }
};

const handleDelete = async (id) => {
    if (confirm('Hapus surat keluar ini?')) {
        try {
            await api.delete(`/surat-keluar/${id}`);
            fetchItems();
        } catch (e) {}
    }
};

const closeModal = () => { showAddModal.value = false; };

onMounted(() => fetchItems(1));
</script>
