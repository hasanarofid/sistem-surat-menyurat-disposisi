<template>
  <div class="space-y-8 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Surat Keluar</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Manajemen pencatatan dan pengiriman surat keluar resmi.</p>
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
              <th class="px-8 py-5">Dokumen</th>
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
                    <div v-if="item.template" class="text-[10px] text-primary bg-primary/10 px-2 py-0.5 rounded-lg inline-block mt-1">
                      📋 {{ item.template.nama_template }}
                    </div>
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
              <td class="px-8 py-6">
                <div v-if="item.file_path">
                  <a :href="getFileUrl(item.file_path)" target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-100 transition-all">
                    <DownloadIcon class="w-3 h-3" />
                    Unduh
                  </a>
                </div>
                <div v-else class="text-[10px] text-slate-300 dark:text-slate-600 font-bold uppercase tracking-widest">Tidak ada</div>
              </td>
              <td class="px-8 py-6 text-center">
                <span :class="statusClass(item)" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                  {{ item.is_signed ? 'Ditandatangani' : item.status }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2 text-nowrap">
                  <button v-if="item.template" @click="handlePrint(item)" class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-indigo-500 transition-all shadow-sm" title="Cetak Surat">
                    <PrinterIcon class="w-4 h-4" />
                  </button>
                  <button v-if="!item.is_signed" @click="handleSign(item.id)" class="p-2.5 bg-primary/10 rounded-xl text-primary hover:bg-primary hover:text-white transition-all shadow-sm" title="Tanda Tangani">
                    <PenToolIcon class="w-4 h-4" />
                  </button>
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
          </div>
          <p>{{ itemToPrint.template.footer_kota }}, {{ formatDate(itemToPrint.tanggal_surat) }}</p>
        </div>

        <!-- TUJUAN -->
        <div class="pt-4">
          <p>Yth.</p>
          <p class="font-bold">{{ itemToPrint.tujuan_surat }}</p>
          <p>di Tempat</p>
        </div>

        <!-- ISI SURAT -->
        <div class="pt-8 min-h-[400px]">
          <div class="whitespace-pre-wrap leading-relaxed text-justify" v-html="renderTemplateContent(itemToPrint)"></div>
        </div>

        <!-- TTD -->
        <div class="flex justify-end pt-12">
          <div class="text-center w-64">
            <p>{{ itemToPrint.template.footer_jabatan_ttd || 'Kepala Instansi' }}</p>
            <div class="h-24 flex items-center justify-center">
              <img v-if="itemToPrint.is_signed && itemToPrint.signature_path" :src="getFileUrl(itemToPrint.signature_path)" class="w-32 h-20 object-contain opacity-80" />
              <div v-else class="h-16"></div>
            </div>
            <p class="font-bold underline">{{ itemToPrint.template.footer_nama_ttd }}</p>
            <p class="text-[10pt]">NIP. {{ itemToPrint.template.footer_nip_ttd }}</p>
          </div>
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
              <div class="flex gap-2"><span class="font-bold w-12">Nomor</span><span>:</span><span class="text-primary font-bold">{{nomor_surat}}</span></div>
              <div class="flex gap-2"><span class="font-bold w-12">Perihal</span><span>:</span><span>{{perihal}}</span></div>
            </div>
            <div class="leading-relaxed whitespace-pre-wrap text-slate-700 min-h-[100px]">{{ previewTemplate.body_template || 'Isi belum diatur.' }}</div>
            <div class="text-right pt-4">
              <p>{{ previewTemplate.footer_kota || 'Kota' }}, <span v-pre>{{tanggal_surat}}</span></p>
              <div class="h-10"></div>
              <p class="font-bold underline">{{ previewTemplate.footer_nama_ttd }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== Modal Form ====== -->
    <Modal :show="showAddModal" :title="editMode ? 'Edit Surat Keluar' : 'Catat Surat Keluar Baru'" @close="closeModal" @submit="handleSubmit">
      <form class="space-y-6">

        <!-- Pilih Template -->
        <div class="space-y-1">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Gunakan Template Surat (Opsional)</label>
          <div class="relative">
            <select v-model="form.template_id" @change="onTemplateChange" class="input-field pr-10">
              <option :value="null">— Tanpa Template —</option>
              <option v-for="tmpl in templates" :key="tmpl.id" :value="tmpl.id">
                {{ tmpl.nama_template }} ({{ tmpl.kode_template || tmpl.jenis_surat }})
              </option>
            </select>
            <LayoutTemplateIcon class="w-4 h-4 text-primary absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
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
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Surat</label>
            <div class="flex gap-2">
              <input v-model="form.nomor_surat" type="text" class="input-field" placeholder="SK/2026/0001">
              <button type="button" @click="generateNumber" class="px-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-primary hover:bg-primary hover:text-white transition-all">
                <RefreshCwIcon class="w-4 h-4" :class="{'animate-spin': generatingNumber}" />
              </button>
            </div>
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
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Unggah Dokumen (PDF/Word/JPG — Maks 10MB)</label>
          <div class="flex items-center justify-center p-10 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all cursor-pointer relative group">
             <input type="file" @change="handleFileUpload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
             <div class="flex flex-col items-center pointer-events-none">
               <CloudUploadIcon class="w-10 h-10 text-primary opacity-20 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 mb-4" />
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ form.file ? form.file.name : 'PDF, Word (.docx), JPG' }}</p>
               <p v-if="form.file" class="text-[9px] text-slate-400 mt-1">{{ formatFileSize(form.file.size) }}</p>
             </div>
          </div>
          <!-- Tombol edit/ganti jika sudah ada file sebelumnya -->
          <div v-if="existingFilePath && !form.file" class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
            <FileCheckIcon class="w-4 h-4 text-green-500" />
            <p class="text-[10px] font-bold text-green-600 flex-1">File dokumen sudah ada. Upload baru untuk mengganti.</p>
            <a :href="getFileUrl(existingFilePath)" target="_blank" class="text-[10px] font-black text-green-700 hover:underline">Lihat File</a>
          </div>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import {
  PlusIcon, SearchIcon, EditIcon, TrashIcon, SendIcon,
  ChevronLeftIcon, ChevronRightIcon, CloudUploadIcon,
  RefreshCwIcon, CheckCircleIcon, PenToolIcon, DownloadIcon,
  LayoutTemplateIcon, FileCheckIcon, XIcon, PrinterIcon
} from 'lucide-vue-next';
import Modal from '@/components/Modal.vue';
import { debounce } from '@/utils/debounce';
import api from '@/api/axios';

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
    '{{tujuan_surat}}': item.tujuan_surat || '',
    '{{perihal}}': item.perihal,
    '{{nama_instansi}}': item.template.kop_nama_instansi,
  };
  
  Object.keys(data).forEach(key => {
    content = content.replaceAll(key, data[key]);
  });
  return content;
};

const handlePrint = (item) => {
  itemToPrint.value = item;
  // Tunggu DOM update lalu cetak
  setTimeout(() => {
    window.print();
    itemToPrint.value = null; // Reset setelah dialog print muncul
  }, 500);
};

const showTemplatePreview = (tmpl) => {
  previewTemplate.value = tmpl;
  showPreview.value = true;
};

const form = reactive({
    nomor_surat: '',
    tanggal_surat: '',
    tujuan_surat: '',
    perihal: '',
    status: 'draft',
    file: null,
    is_auto_generated: false,
    template_id: null,
});

const selectedTemplate = computed(() => {
  if (!form.template_id) return null;
  return templates.value.find(t => t.id === form.template_id) || null;
});

const onTemplateChange = () => {
  if (selectedTemplate.value) {
    // Auto-fill kop info namun jangan overwrite jika sudah isi
    if (!form.perihal && selectedTemplate.value.deskripsi) {
      form.perihal = selectedTemplate.value.deskripsi;
    }
  }
};

const statusClass = (item) => {
  if (item.is_signed) return 'text-primary bg-primary/10 border-primary/20';
  return item.status === 'sent' ? 'text-green-500 bg-green-50 dark:bg-green-900/30' : 'text-slate-500 bg-slate-50 dark:bg-slate-900/30';
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const formatFileSize = (bytes) => {
  if (!bytes) return '';
  const kb = bytes / 1024;
  if (kb < 1024) return `${kb.toFixed(1)} KB`;
  return `${(kb / 1024).toFixed(1)} MB`;
};

const getFileUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('/storage/')) return path;
  if (path.startsWith('storage/')) return `/${path}`;
  return `/storage/${path}`;
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

const fetchTemplates = async () => {
  try {
    const { data } = await api.get('/template-surat', { params: { per_page: 100, is_active: true } });
    templates.value = data.data || [];
  } catch (err) {}
};

const generateNumber = async () => {
    generatingNumber.value = true;
    try {
        const { data } = await api.get('/surat-keluar/generate-number');
        form.nomor_surat = data.number;
        form.is_auto_generated = true;
    } catch (err) {
        alert('Gagal generate nomor');
    } finally {
        generatingNumber.value = false;
    }
};

const handleSearch = debounce(() => { fetchItems(1); }, 500);

const handleFileUpload = (e) => { form.file = e.target.files[0]; };

const openModal = (item = null) => {
    if (item) {
        editMode.value = true;
        selectedId.value = item.id;
        existingFilePath.value = item.file_path || null;
        Object.assign(form, {
            nomor_surat: item.nomor_surat,
            tanggal_surat: item.tanggal_surat,
            tujuan_surat: item.tujuan_surat,
            perihal: item.perihal,
            status: item.status,
            file: null,
            is_auto_generated: false,
            template_id: item.template_id || null,
        });
    } else {
        editMode.value = false;
        selectedId.value = null;
        existingFilePath.value = null;
        Object.assign(form, {
            nomor_surat: '', tanggal_surat: '', tujuan_surat: '', perihal: '',
            status: 'draft', file: null, is_auto_generated: false, template_id: null,
        });
    }
    showAddModal.value = true;
};

const handleSubmit = async () => {
    const formData = new FormData();
    formData.append('nomor_surat', form.nomor_surat);
    formData.append('tanggal_surat', form.tanggal_surat);
    formData.append('tujuan_surat', form.tujuan_surat);
    formData.append('perihal', form.perihal);
    formData.append('status', form.status);
    formData.append('is_auto_generated', form.is_auto_generated ? '1' : '0');
    if (form.template_id) formData.append('template_id', form.template_id);
    if (form.file) formData.append('file', form.file);

    try {
        if (editMode.value) {
            await api.post(`/surat-keluar/${selectedId.value}?_method=PUT`, formData);
        } else {
            await api.post('/surat-keluar', formData);
        }
        closeModal();
        fetchItems(pagination.value.current_page);
    } catch (err) {
        alert('Gagal memproses data. Silakan periksa kembali input Anda.');
    }
};

const handleSign = async (id) => {
    if (confirm('Tanda tangani surat ini secara elektronik?')) {
        try {
            await api.post(`/surat-keluar/${id}/sign`);
            fetchItems(pagination.value.current_page);
        } catch (err) {
            alert('Gagal menandatangani surat. Pastikan Anda sudah mengatur tanda tangan di profil.');
        }
    }
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

onMounted(() => {
  fetchItems(1);
  fetchTemplates();
});
</script>
