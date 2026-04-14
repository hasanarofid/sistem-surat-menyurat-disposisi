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
        <div class="flex items-center bg-slate-100/50 dark:bg-slate-800/50 rounded-2xl px-6 py-3 w-full max-w-lg group focus-within:bg-white dark:focus-within:bg-slate-800 focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-500">
          <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
          <input 
            v-model="search" 
            @input="handleSearch"
            type="text" 
            placeholder="Cari nama atau email..." 
            class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium" 
          />
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
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
          <input v-model="form.name" type="text" class="input-field" placeholder="Masukkan nama lengkap...">
        </div>
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
          <input v-model="form.email" type="email" class="input-field" placeholder="nama@blu.com">
        </div>
        
        <!-- Password Field with Visibility Toggle -->
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password {{ editMode ? '(Kosongkan jika tidak diubah)' : '' }}</label>
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
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Hak Akses (Role)</label>
          <SearchableSelect 
            v-model="form.role_id" 
            :options="roles" 
            placeholder="Pilih peranan pengguna..." 
          />
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { 
  SearchIcon, UserPlusIcon, ShieldIcon, ShieldCheckIcon, EditIcon, TrashIcon, 
  EyeIcon, EyeOffIcon, ChevronLeftIcon, ChevronRightIcon 
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { debounce } from '@/utils/debounce';

const items = ref([]);
const roles = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const search = ref('');
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
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const fetchData = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/users', {
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
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        Object.assign(form, { name: '', email: '', password: '', role_id: '' });
    }
    showModal.value = true;
};

const handleSubmit = async () => {
    try {
        if (editMode.value) {
            await api.put(`/users/${selectedId.value}`, form);
        } else {
            await api.post('/users', form);
        }
        showModal.value = false;
        fetchData(pagination.value.current_page);
    } catch (e) {
        alert('Gagal memproses data pengguna.');
    }
};

const handleDelete = async (id) => {
    if (confirm('Hapus pengguna ini?')) {
        try {
            await api.delete(`/users/${id}`);
            fetchData();
        } catch (e) { alert('Gagal menghapus'); }
    }
};

onMounted(() => {
  fetchData();
  fetchRoles();
});
</script>
