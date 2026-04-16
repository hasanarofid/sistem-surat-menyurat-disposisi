<template>
  <div class="space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Manajemen Pengguna</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Kelola data pengguna, hak akses, dan peranan dalam sistem E-Office.</p>
      </div>
      <button @click="openModal()" class="btn-primary group flex items-center gap-3">
        <UserPlusIcon class="w-5 h-5 transition-transform group-hover:scale-125" />
        <span>Tambah Pengguna</span>
      </button>
    </div>

    <!-- Quick Stats for RBAC -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="stat in rbacStats" :key="stat.role" class="card relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full blur-xl transition-all group-hover:bg-primary/10"></div>
        <div class="flex items-center gap-5">
           <div :class="`w-14 h-14 rounded-2xl flex items-center justify-center bg-${stat.color}-50 dark:bg-${stat.color}-900/20 text-${stat.color}-600 dark:text-${stat.color}-400 shadow-inner group-hover:scale-110 transition-all duration-500`">
             <ShieldIcon class="w-6 h-6" />
           </div>
           <div>
             <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Porsi {{ stat.role }}</div>
             <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1 group-hover:text-primary transition-colors tracking-tighter">{{ stat.count }}%</h3>
           </div>
        </div>
      </div>
    </div>

    <div class="card p-0 overflow-hidden border-none shadow-premium bg-white dark:bg-slate-900 relative">
      <div v-if="loading" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] z-10 flex items-center justify-center">
        <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center bg-white/50 dark:bg-slate-900/60 backdrop-blur-xl rounded-full px-6 py-2.5 w-full max-lg group transition-all duration-500 border border-slate-200/50 dark:border-slate-800 focus-within:border-accent-blue focus-within:bg-white dark:focus-within:bg-slate-950 focus-within:scale-[1.01] focus-within:shadow-[0_0_20px_-5px_rgba(59,130,246,0.5)] ring-4 ring-transparent focus-within:ring-accent-blue/10">
          <div class="relative flex items-center justify-center">
            <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-accent-blue transition-all duration-500" />
            <div class="absolute inset-0 bg-accent-blue/20 blur-xl opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity"></div>
          </div>
          <input 
            v-model="search" 
            @input="handleSearch"
            type="text" 
            placeholder="Cari nama atau email..." 
            class="bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] ml-4 w-full text-slate-700 dark:text-slate-200 placeholder-slate-400/50 font-bold tracking-tight" 
          />
          <transition name="fade">
            <div v-if="loading" class="flex items-center gap-3 pl-4 border-l border-slate-100 dark:border-slate-800">
               <div class="flex gap-1">
                  <div class="w-1 h-3 bg-primary rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                  <div class="w-1 h-3 bg-primary rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                  <div class="w-1 h-3 bg-primary rounded-full animate-bounce"></div>
               </div>
               <span class="text-[9px] font-black text-primary uppercase tracking-[0.2em] animate-pulse">Searching</span>
            </div>
          </transition>
        </div>
        <div class="flex items-center bg-white dark:bg-slate-900/60 backdrop-blur-xl rounded-full px-4 py-2 border border-slate-200/50 dark:border-slate-800 shadow-soft transition-all hover:border-accent-blue/30 group">
           <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mr-3 ml-1">Tampilkan:</span>
           <select v-model="perPage" @change="fetchData(1)" class="bg-transparent border-none text-xs font-black p-0 focus:ring-0 text-slate-700 dark:text-slate-300 cursor-pointer min-w-[80px]">
             <option :value="10" class="dark:bg-slate-900">10 Baris</option>
             <option :value="25" class="dark:bg-slate-900">25 Baris</option>
             <option :value="50" class="dark:bg-slate-900">50 Baris</option>
           </select>
        </div>
      </div>

      <div class="overflow-x-auto text-nowrap">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50 dark:bg-slate-800/30">
              <th class="px-8 py-5">Informasi Pengguna</th>
              <th class="px-8 py-5">Hak Akses (Role)</th>
              <th class="px-8 py-5">Status Akun</th>
              <th class="px-8 py-5">Dibuat Pada</th>
              <th class="px-8 py-5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/30">
            <tr v-for="user in items" :key="user.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300">
              <td class="px-8 py-6">
                <div class="flex items-center gap-5">
                   <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black shadow-inner">
                     {{ user.name.charAt(0).toUpperCase() }}
                   </div>
                   <div>
                     <div class="text-[13px] font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors cursor-pointer">{{ user.name }}</div>
                     <div class="text-[11px] font-bold text-slate-400 mt-1 lowercase">{{ user.email }}</div>
                   </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center gap-2">
                  <ShieldCheckIcon class="w-4 h-4 text-primary opacity-50" />
                  <span class="text-[12px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-tight">{{ user.roles?.[0]?.name || 'Staff' }}</span>
                </div>
              </td>
              <td class="px-8 py-6">
                 <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                   <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                   Aktif
                 </span>
              </td>
              <td class="px-8 py-6 text-[12px] font-bold text-slate-500 dark:text-slate-400">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openModal(user)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary hover:bg-white dark:hover:bg-slate-700 transition-all">
                    <EditIcon class="w-4 h-4" />
                  </button>
                  <button @click="handleDelete(user.id)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-700 transition-all">
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
           {{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }} user
         </span>
         <div class="flex items-center gap-2">
           <button @click="fetchItems(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-30">
             <ChevronLeftIcon class="w-4 h-4" />
           </button>
           <button @click="fetchItems(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-30">
             <ChevronRightIcon class="w-4 h-4" />
           </button>
         </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Modal :show="showModal" :title="editMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'" @close="showModal = false" @submit="handleSubmit">
      <form class="space-y-6">
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
          <input v-model="form.name" type="text" class="input-field" placeholder="Masukkan nama lengkap...">
        </div>
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
          <input v-model="form.email" type="email" class="input-field" placeholder="nama@blu.com">
        </div>
        
        <!-- Password Field with Visibility Toggle -->
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Password {{ editMode ? '(Kosongkan jika tidak diubah)' : '' }}</label>
          <div class="relative group">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              class="input-field pr-12 focus:shadow-glow" 
              placeholder="Minimal 8 karakter..."
            >
            <button 
              type="button" 
              @click="showPassword = !showPassword"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors h-10 w-10 flex items-center justify-center rounded-xl hover:bg-primary/5"
            >
              <EyeIcon v-if="!showPassword" class="w-5 h-5" />
              <EyeOffIcon v-else class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Searchable Select for Roles -->
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Hak Akses (Role)</label>
          <SearchableSelect 
            v-model="form.role_id" 
            :options="roles" 
            placeholder="Pilih peranan pengguna..." 
          />
        </div>

        <!-- Signature Upload -->
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Tanda Tangan Elektronik (PNG/JPG)</label>
          <div class="flex items-center gap-6 p-6 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-3xl bg-slate-50/50 dark:bg-slate-950 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer relative group">
             <input type="file" @change="handleFileUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
             <div v-if="!form.signature_preview" class="flex items-center gap-4 pointer-events-none">
               <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-all duration-500">
                 <PenToolIcon class="w-6 h-6" />
               </div>
               <div>
                  <p class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Pilih Berkas</p>
                  <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Maks 1MB, format transparan disarankan</p>
               </div>
             </div>
             <div v-else class="flex items-center gap-6 w-full">
                <img :src="form.signature_preview" class="h-16 w-auto object-contain bg-white rounded-xl shadow-soft p-2 mix-blend-multiply border border-slate-100" />
                <div class="flex-1">
                   <p class="text-[10px] font-black text-primary uppercase tracking-widest">Berkas Terpilih</p>
                   <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Klik lagi untuk mengganti</p>
                </div>
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
  SearchIcon, UserPlusIcon, ShieldIcon, ShieldCheckIcon, EditIcon, TrashIcon, 
  EyeIcon, EyeOffIcon, ChevronLeftIcon, ChevronRightIcon, PenToolIcon 
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { debounce } from '@/utils/debounce';
import api from '@/api/axios';
import Alert from '@/utils/alert';

const items = ref([]);
const roles = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const search = ref('');
const perPage = ref(10);
const showPassword = ref(false);
const pagination = ref({});

const rbacStats = [
  { role: 'Admin', count: 12, color: 'primary' },
  { role: 'Pimpinan', count: 8, color: 'rose' },
  { role: 'Internal', count: 80, color: 'success' },
];

const form = reactive({
  name: '',
  email: '',
  password: '',
  role_id: '',
  signature_image: null,
  signature_preview: null,
});

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.signature_image = file;
        form.signature_preview = URL.createObjectURL(file);
    }
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchData = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/users', {
            params: { page, search: search.value, per_page: perPage.value }
        });
        items.value = data.data;
        pagination.value = data;
    } catch (e) {
        console.error('Fetch error', e);
    } finally {
        loading.value = false;
    }
};

const fetchRoles = async () => {
  try {
    const { data } = await api.get('/roles');
    roles.value = data;
  } catch (e) { console.error(e); }
};

const handleSearch = debounce(() => {
    fetchData(1);
}, 500);

const openModal = (user = null) => {
    showPassword.value = false;
    if (user) {
        editMode.value = true;
        selectedId.value = user.id;
        Object.assign(form, {
            name: user.name,
            email: user.email,
            password: '',
            role_id: user.roles?.[0]?.id || '',
            signature_image: null,
            signature_preview: user.signature_image, // API returns URL
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        Object.assign(form, { name: '', email: '', password: '', role_id: '', signature_image: null, signature_preview: null });
    }
    showModal.value = true;
};

const handleSubmit = async () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('email', form.email);
    if (form.password) formData.append('password', form.password);
    formData.append('role_id', form.role_id);
    if (form.signature_image) formData.append('signature_image', form.signature_image);

    try {
        if (editMode.value) {
            await api.post(`/users/${selectedId.value}?_method=PUT`, formData);
            Alert.success('Data pengguna berhasil diperbarui.');
        } else {
            await api.post('/users', formData);
            Alert.success('Pengguna baru berhasil ditambahkan.');
        }
        showModal.value = false;
        fetchData(pagination.value.current_page);
    } catch (e) {
        Alert.error('Gagal memproses data pengguna.');
    }
};

const handleDelete = async (id) => {
    const result = await Alert.confirm('Apakah Anda yakin ingin menghapus pengguna ini?');
    if (result.isConfirmed) {
        try {
            await api.delete(`/users/${id}`);
            Alert.success('Pengguna berhasil dihapus.');
            fetchData();
        } catch (e) { Alert.error('Gagal menghapus pengguna.'); }
    }
};

onMounted(() => {
  fetchData();
  fetchRoles();
});
</script>
