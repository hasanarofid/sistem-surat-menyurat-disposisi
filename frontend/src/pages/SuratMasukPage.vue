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
      <div class="flex items-center bg-white/50 dark:bg-slate-900/60 backdrop-blur-xl rounded-full px-6 py-2.5 w-full max-w-xl group transition-all duration-500 border border-slate-200/50 dark:border-slate-800 focus-within:border-accent-blue focus-within:bg-white dark:focus-within:bg-slate-950 focus-within:scale-[1.01] focus-within:shadow-[0_0_20px_-5px_rgba(59,130,246,0.5)] ring-4 ring-transparent focus-within:ring-accent-blue/10">
        <div class="relative flex items-center justify-center">
          <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-accent-blue transition-all duration-500" />
          <div class="absolute inset-0 bg-accent-blue/20 blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
        </div>
        <input 
          v-model="search" 
          @input="handleSearch"
          type="text" 
          placeholder="Cari nomor surat atau tujuan..." 
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
         <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mr-3 ml-1">Urutkan:</span>
         <select v-model="perPage" @change="fetchItems(1)" class="bg-transparent border-none text-xs font-black p-0 focus:ring-0 text-slate-700 dark:text-slate-300 cursor-pointer min-w-[80px]">
           <option :value="10" class="dark:bg-slate-900">10 Baris</option>
           <option :value="25" class="dark:bg-slate-900">25 Baris</option>
           <option :value="50" class="dark:bg-slate-900">50 Baris</option>
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
                    <div class="flex items-center gap-2 mt-1">
                      <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate max-w-[200px]">{{ item.perihal }}</div>
                      <span v-if="item.template" class="px-2 py-0.5 rounded-lg bg-primary/10 text-primary text-[9px] font-black uppercase tracking-tighter">
                        {{ item.template.nama_template }}
                      </span>
                    </div>
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
                <div class="flex items-center justify-end gap-2 text-nowrap">
                  <button v-if="item.template" @click="handlePrint(item)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-indigo-500 transition-all shadow-sm" title="Cetak Surat">
                    <PrinterIcon class="w-4 h-4" />
                  </button>
                  <a v-if="item.file_path" :href="getFileUrl(item.file_path)" target="_blank"
                    class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-green-500 transition-all shadow-sm" title="Unduh Dokumen">
                    <DownloadIcon class="w-4 h-4" />
                  </a>
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

    <!-- ========== MODAL PREVIEW TEMPLATE ========== -->
    <div v-if="showPreview && previewTemplate" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" @click="showPreview = false"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-3xl w-full max-w-2xl max-h-[85vh] overflow-hidden shadow-2xl flex flex-col">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <h3 class="font-black text-slate-800 dark:text-white">Pratinjau Layout Template</h3>
          <button @click="showPreview = false" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
            <XIcon class="w-5 h-5" />
          </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6 bg-slate-50/50 dark:bg-slate-950/20">
          <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm font-serif text-[12px] text-slate-800 space-y-4">
            <div class="border-b-2 border-slate-800 pb-3 text-center">
              <div class="text-sm font-black uppercase tracking-wide">{{ previewTemplate.kop_nama_instansi || 'NAMA INSTANSI' }}</div>
              <div class="text-[10px] mt-0.5">{{ previewTemplate.kop_alamat || 'Alamat Instansi' }}</div>
            </div>
            <div class="space-y-1" v-pre>
              <div class="flex gap-2"><span class="font-bold w-12 text-slate-500">Nomor</span><span>:</span><span class="text-primary font-bold">{{nomor_surat}}</span></div>
              <div class="flex gap-2"><span class="font-bold w-12 text-slate-500">Perihal</span><span>:</span><span>{{perihal}}</span></div>
            </div>
            <div class="leading-relaxed whitespace-pre-wrap text-slate-700 min-h-[100px] italic">{{ previewTemplate.body_template || 'Isi belum diatur.' }}</div>
            <div class="text-right pt-4">
              <p>{{ previewTemplate.footer_kota || 'Kota' }}, <span v-pre>{{tanggal_surat}}</span></p>
              <div class="h-10"></div>
              <p class="font-bold underline">{{ previewTemplate.footer_nama_ttd }}</p>
              <p class="text-[10px] text-slate-400">{{ previewTemplate.footer_nip_ttd }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== OVERLAY CETAK / PRINT VIEW ========== -->
    <div id="print-area" v-if="itemToPrint" class="hidden print:block fixed inset-0 bg-white z-[9999] p-0 m-0">
      <div class="p-12 font-serif text-[12pt] text-black leading-normal space-y-6">
        <!-- KOP SURAT -->
        <div class="border-b-4 border-black pb-4 text-center relative">
          <img v-if="itemToPrint.template.kop_logo_path" :src="getFileUrl(itemToPrint.template.kop_logo_path)" class="absolute left-0 top-0 w-24 h-24 object-contain" />
          <div class="pl-28 pr-12">
            <h2 class="text-xl font-bold uppercase">{{ itemToPrint.template.kop_nama_instansi }}</h2>
            <p class="text-[10pt] mt-1">{{ itemToPrint.template.kop_alamat }}</p>
            <p class="text-[9pt] italic">Telp: {{ itemToPrint.template.kop_telp }} | Email: {{ itemToPrint.template.kop_email }}</p>
          </div>
        </div>

        <!-- NOMOR & TANGGAL -->
        <div class="flex justify-between items-start pt-4">
          <div class="space-y-1">
            <p><strong>Nomor :</strong> {{ itemToPrint.nomor_surat }}</p>
            <p><strong>Perihal :</strong> {{ itemToPrint.perihal }}</p>
            <p><strong>Asal :</strong> {{ itemToPrint.asal_surat }}</p>
          </div>
          <p>{{ itemToPrint.template.footer_kota }}, {{ formatDate(itemToPrint.tanggal_terima) }}</p>
        </div>

        <!-- ISI SURAT (KATEGORI MASUK) -->
        <div class="pt-8 min-h-[400px]">
          <div class="whitespace-pre-wrap leading-relaxed text-justify" v-html="renderTemplateContent(itemToPrint)"></div>
        </div>

        <!-- TTD (BIASANYA OLEH PENERIMA/PIMPINAN INTERNAL) -->
        <div class="flex justify-end pt-12">
          <div class="text-center w-64">
            <p>{{ itemToPrint.template.footer_jabatan_ttd || 'Staf Administrasi' }}</p>
            <div class="h-24"></div>
            <p class="font-bold underline">{{ itemToPrint.template.footer_nama_ttd }}</p>
            <p class="text-[10pt]">NIP. {{ itemToPrint.template.footer_nip_ttd }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Professional Modal -->
    <Modal :show="showAddModal" :title="editMode ? 'Edit Surat Masuk' : 'Tambah Surat Baru'" @close="closeModal" @submit="handleSubmit">
      <form class="space-y-6">
        <!-- Template & Auto Numbering -->
        <div v-if="!editMode" class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 space-y-4">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Gunakan Layout Template (Opsional)</label>
            <div class="relative">
              <select v-model="form.template_id" @change="onTemplateChange" class="input-field appearance-none pr-10">
                <option :value="null">-- Tanpa Template --</option>
                <option v-for="t in templates" :key="t.id" :value="t.id">{{ t.nama_template }} ({{ t.kode_template || 'No Code' }})</option>
              </select>
              <LayoutTemplateIcon class="w-4 h-4 text-primary absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>
          </div>
          <div v-if="selectedTemplate" class="flex flex-col gap-3 p-3 bg-primary/5 rounded-2xl border border-primary/10">
            <div class="flex items-start gap-3">
              <div class="w-6 h-6 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5">
                <CheckCircleIcon class="w-4 h-4" />
              </div>
              <div class="flex-1">
                <p class="text-[11px] font-black text-slate-700 dark:text-slate-200">{{ selectedTemplate.nama_template }}</p>
                <p v-if="selectedTemplate.deskripsi" class="text-[10px] text-slate-400 mt-0.5">{{ selectedTemplate.deskripsi }}</p>
              </div>
              <button type="button" @click="showTemplatePreview(selectedTemplate)" class="text-[10px] font-black text-primary hover:underline px-2">Preview</button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Nomor Surat</label>
            <div class="flex gap-2">
              <input v-model="form.nomor_surat" type="text" class="input-field" placeholder="SM/2026/0001">
              <button v-if="!editMode" type="button" @click="generateNumber" :disabled="generatingNumber" class="p-3 bg-primary text-white rounded-xl hover:shadow-glow transition-all disabled:opacity-50">
                <RefreshCwIcon class="w-4 h-4" :class="{'animate-spin': generatingNumber}" />
              </button>
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Kategori / Status</label>
            <select v-model="form.status" class="input-field">
              <option value="pending">PENDING</option>
              <option value="processed">PROCESSED</option>
              <option value="archived">ARCHIVED</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Tanggal Surat</label>
            <input v-model="form.tanggal_surat" type="date" class="input-field">
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Tanggal Terima</label>
            <input v-model="form.tanggal_terima" type="date" class="input-field">
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Asal Surat / Instansi</label>
          <input v-model="form.asal_surat" type="text" class="input-field" placeholder="PT Contoh Jaya Abadi">
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">Perihal / Isi Ringkas</label>
          <textarea v-model="form.perihal" class="input-field h-24" placeholder="Keterangan perihal surat..."></textarea>
        </div>

        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">File Dokumen (PDF/Word/JPG — Maks 10MB)</label>
          <div class="flex items-center justify-center p-8 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all cursor-pointer relative overflow-hidden group">
             <input type="file" @change="handleFileUpload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
             <div class="flex flex-col items-center pointer-events-none">
               <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                 <CloudUploadIcon class="w-6 h-6" />
               </div>
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ form.file ? form.file.name : 'Klik atau tarik file ke sini' }}</p>
               <p class="text-[9px] text-slate-400 mt-2 italic">PDF, Word (.docx), JPG (Max 10MB)</p>
             </div>
          </div>
          <div v-if="existingFilePath && !form.file" class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
            <CheckCircleIcon class="w-4 h-4 text-green-500 shrink-0" />
            <p class="text-[10px] font-bold text-green-600 flex-1">File sudah ada. Upload baru untuk mengganti.</p>
            <a :href="getFileUrl(existingFilePath)" target="_blank" class="text-[10px] font-black text-green-700 hover:underline">Lihat File</a>
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
  InboxIcon, PlusIcon, SearchIcon, EditIcon, TrashIcon, SendIcon,
  ChevronLeftIcon, ChevronRightIcon, CloudUploadIcon, DownloadIcon,
  CheckCircleIcon, LayoutTemplateIcon, RefreshCwIcon, XIcon, PrinterIcon
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import { debounce } from '@/utils/debounce';
import api from '@/api/axios';

const router = useRouter();

const items = ref([]);
const templates = ref([]);
const loading = ref(false);
const showPreview = ref(false);
const previewTemplate = ref(null);
const generatingNumber = ref(false);
const showAddModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const existingFilePath = ref(null);
const search = ref('');
const perPage = ref(10);
const pagination = ref({});
const itemToPrint = ref(null);

const renderTemplateContent = (item) => {
  if (!item.template || !item.template.body_template) return '';
  let content = item.template.body_template;
  const data = {
    '{{nomor_surat}}': item.nomor_surat,
    '{{tanggal_surat}}': formatDate(item.tanggal_surat),
    '{{perihal}}': item.perihal,
    '{{asal_surat}}': item.asal_surat,
    '{{nama_instansi}}': item.template.kop_nama_instansi,
  };
  
  Object.keys(data).forEach(key => {
    content = content.replaceAll(key, data[key]);
  });
  return content;
};

const handlePrint = (item) => {
  itemToPrint.value = item;
  setTimeout(() => {
    window.print();
    itemToPrint.value = null;
  }, 500);
};

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

const getFileUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('/storage/')) return path;
  if (path.startsWith('storage/')) return `/${path}`;
  return `/storage/${path}`;
};

const form = reactive({
    nomor_surat: '',
    tanggal_surat: '',
    tanggal_terima: '',
    asal_surat: '',
    perihal: '',
    status: 'pending',
    template_id: null,
    is_auto_generated: false,
    file: null,
});

const selectedTemplate = computed(() => {
  if (!form.template_id) return null;
  return templates.value.find(t => t.id === form.template_id);
});

const onTemplateChange = () => {
  if (selectedTemplate.value) {
    if (!form.perihal && selectedTemplate.value.deskripsi) {
      form.perihal = selectedTemplate.value.deskripsi;
    }
  }
};

const showTemplatePreview = (tmpl) => {
  previewTemplate.value = tmpl;
  showPreview.value = true;
};

const generateNumber = async () => {
  generatingNumber.value = true;
  try {
    const { data } = await api.get('/surat-masuk/generate-number');
    form.nomor_surat = data.number;
    form.is_auto_generated = true;
  } catch (err) {
    alert('Gagal membuat nomor otomatis');
  } finally {
    generatingNumber.value = false;
  }
};

const fetchTemplates = async () => {
  try {
    const { data } = await api.get('/template-surat', { params: { jenis_surat: 'masuk', per_page: 100 } });
    templates.value = data.data;
  } catch (err) {
    console.error('Fetch templates error', err);
  }
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
    if (form.template_id) formData.append('template_id', form.template_id);
    if (form.is_auto_generated) formData.append('is_auto_generated', '1');
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
    fetchTemplates();
    if (item) {
        editMode.value = true;
        selectedId.value = item.id;
        existingFilePath.value = item.file_path || null;
        Object.assign(form, {
            nomor_surat: item.nomor_surat,
            tanggal_surat: item.tanggal_surat,
            tanggal_terima: item.tanggal_terima,
            asal_surat: item.asal_surat,
            perihal: item.perihal,
            status: item.status,
            template_id: item.template_id || null,
            is_auto_generated: false,
            file: null
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        existingFilePath.value = null;
        Object.assign(form, {
            nomor_surat: '',
            tanggal_surat: new Date().toISOString().split('T')[0],
            tanggal_terima: new Date().toISOString().split('T')[0],
            asal_surat: '',
            perihal: '',
            status: 'pending',
            template_id: null,
            is_auto_generated: false,
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
  if (!last) return [1];
  
  let start = Math.max(1, current - 2);
  let end = Math.min(last, current + 2);
  
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

onMounted(() => fetchItems(1));
</script>
