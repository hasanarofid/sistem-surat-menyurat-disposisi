<template>
  <div class="space-y-8 pb-24">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Template Surat</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Kelola template tata letak surat resmi instansi.</p>
      </div>
      <button @click="openModal()" class="btn-primary group flex items-center gap-3">
        <PlusIcon class="w-5 h-5 transition-transform group-hover:rotate-90" />
        <span>Buat Template Baru</span>
      </button>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="flex items-center bg-white dark:bg-slate-900 rounded-2xl px-6 py-3 w-full max-w-xl shadow-soft group focus-within:shadow-glow border border-transparent focus-within:border-primary/20 transition-all duration-300">
        <SearchIcon class="w-5 h-5 text-slate-400 group-focus-within:text-primary transition-colors" />
        <input
          v-model="search"
          @input="handleSearch"
          type="text"
          placeholder="Cari nama atau kode template..."
          class="bg-transparent border-none focus:ring-0 text-sm ml-4 w-full font-medium"
        />
      </div>
      <div class="flex items-center gap-3">
        <select v-model="filterJenis" @change="fetchItems(1)" class="bg-white dark:bg-slate-900 border-none rounded-xl text-xs font-black p-2 shadow-soft focus:ring-primary/20">
          <option value="">Semua Jenis</option>
          <option value="keluar">Surat Keluar</option>
          <option value="masuk">Surat Masuk</option>
          <option value="umum">Umum</option>
        </select>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && items.length === 0" class="text-center py-20">
      <div class="w-20 h-20 rounded-3xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-6">
        <LayoutTemplateIcon class="w-10 h-10" />
      </div>
      <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Belum Ada Template</h3>
      <p class="text-sm text-slate-400 font-medium mb-6">Mulai buat template surat pertama untuk instansi Anda.</p>
      <button @click="openModal()" class="btn-primary">Buat Template Sekarang</button>
    </div>

    <!-- Template Cards Grid -->
    <div v-if="!loading && items.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="item in items" :key="item.id"
        class="group bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-soft hover:shadow-premium transition-all duration-300 hover:-translate-y-1 relative overflow-hidden"
      >
        <!-- Background accent -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -translate-x-4 -translate-y-10 group-hover:scale-150 transition-transform duration-500"></div>

        <!-- Jenis Badge -->
        <div class="flex items-center justify-between mb-4">
          <span :class="jenisBadge(item.jenis_surat)" class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest">
            {{ item.jenis_surat }}
          </span>
          <div class="flex items-center gap-1">
            <span v-if="item.is_active" class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            <span v-else class="w-2 h-2 rounded-full bg-slate-300"></span>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span>
          </div>
        </div>

        <!-- Icon & Title -->
        <div class="flex items-start gap-4 mb-4">
          <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
            <FileTextIcon class="w-6 h-6" />
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-black text-slate-800 dark:text-white text-sm leading-tight">{{ item.nama_template }}</h3>
            <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-lg mt-1 inline-block">{{ item.kode_template || 'Tanpa Kode' }}</span>
          </div>
        </div>

        <p class="text-xs text-slate-400 font-medium leading-relaxed mb-4 line-clamp-2">
          {{ item.deskripsi || 'Tidak ada deskripsi.' }}
        </p>

        <!-- Kop Info -->
        <div v-if="item.kop_nama_instansi" class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-3 mb-4">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kop Surat</p>
          <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ item.kop_nama_instansi }}</p>
          <p v-if="item.kop_alamat" class="text-[10px] text-slate-400 truncate">{{ item.kop_alamat }}</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button @click="openPreview(item)" class="flex-1 flex items-center justify-center gap-2 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-xs font-bold">
            <EyeIcon class="w-4 h-4" />
            Preview
          </button>
          <button @click="openModal(item)" class="flex-1 flex items-center justify-center gap-2 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-primary hover:bg-primary/10 transition-all text-xs font-bold">
            <EditIcon class="w-4 h-4" />
            Edit
          </button>
          <button @click="handleDelete(item.id)" class="p-2 bg-slate-50 dark:bg-slate-800 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
            <TrashIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.last_page > 1" class="flex items-center justify-between">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
        {{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }} template
      </span>
      <div class="flex items-center gap-2">
        <button @click="fetchItems(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="p-2.5 rounded-xl bg-white dark:bg-slate-900 shadow-soft disabled:opacity-30">
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <button @click="fetchItems(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="p-2.5 rounded-xl bg-white dark:bg-slate-900 shadow-soft disabled:opacity-30">
          <ChevronRightIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- ========== MODAL CREATE/EDIT ========== -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-3xl w-full max-w-4xl my-8 shadow-2xl overflow-hidden">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-primary/5 to-accent-indigo/5">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
              <LayoutTemplateIcon class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-lg font-black text-slate-800 dark:text-white">{{ editMode ? 'Edit Template' : 'Buat Template Baru' }}</h3>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Template Builder</p>
            </div>
          </div>
          <button @click="closeModal" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-all">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
          <button v-for="tab in formTabs" :key="tab.id" @click="activeFormTab = tab.id"
            class="flex items-center gap-2 px-6 py-4 text-xs font-black uppercase tracking-widest transition-all border-b-2"
            :class="activeFormTab === tab.id ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            {{ tab.name }}
          </button>
        </div>

        <!-- Tab: Informasi Dasar -->
        <div v-if="activeFormTab === 'info'" class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1">
              <label class="label-field">Nama Template <span class="text-red-400">*</span></label>
              <input v-model="form.nama_template" type="text" class="input-field" placeholder="Template Surat Undangan" />
            </div>
            <div class="space-y-1">
              <label class="label-field">Kode Template</label>
              <input v-model="form.kode_template" type="text" class="input-field" placeholder="TMPL-001" />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1">
              <label class="label-field">Jenis Surat</label>
              <select v-model="form.jenis_surat" class="input-field">
                <option value="umum">Umum</option>
                <option value="keluar">Surat Keluar</option>
                <option value="masuk">Surat Masuk</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="label-field">Status</label>
              <select v-model="form.is_active" class="input-field">
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif</option>
              </select>
            </div>
          </div>
          <div class="space-y-1">
            <label class="label-field">Deskripsi Template</label>
            <textarea v-model="form.deskripsi" class="input-field h-24" placeholder="Deskripsi singkat template ini..."></textarea>
          </div>
        </div>

        <!-- Tab: Kop Surat -->
        <div v-if="activeFormTab === 'kop'" class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
          <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800">
            <p class="text-xs font-bold text-blue-600 dark:text-blue-400">
              💡 Kop surat akan tampil di bagian atas dokumen surat yang dihasilkan dari template ini.
            </p>
          </div>
          <div class="space-y-1">
            <label class="label-field">Nama Instansi / Lembaga</label>
            <input v-model="form.kop_nama_instansi" type="text" class="input-field" placeholder="BLU RSUD Dr. Soetomo" />
          </div>
          <div class="space-y-1">
            <label class="label-field">Alamat Instansi</label>
            <input v-model="form.kop_alamat" type="text" class="input-field" placeholder="Jl. Mayjend Prof. Dr. Moestopo No.6-8, Surabaya" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1">
              <label class="label-field">Nomor Telepon</label>
              <input v-model="form.kop_telp" type="text" class="input-field" placeholder="(031) 5501078" />
            </div>
            <div class="space-y-1">
              <label class="label-field">Email Instansi</label>
              <input v-model="form.kop_email" type="email" class="input-field" placeholder="info@rsud.surabaya.go.id" />
            </div>
          </div>
          <div class="space-y-1">
            <label class="label-field">Website</label>
            <input v-model="form.kop_website" type="text" class="input-field" placeholder="www.rsud-dr-soetomo.surabaya.go.id" />
          </div>
          <div class="space-y-1">
            <label class="label-field">Logo Instansi (PNG/JPG, maks 1MB)</label>
            <div class="flex items-center justify-center p-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all cursor-pointer relative">
              <input type="file" @change="handleLogoUpload" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
              <div class="flex flex-col items-center pointer-events-none">
                <ImageIcon class="w-8 h-8 text-primary opacity-40 mb-2" />
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ form.kop_logo ? form.kop_logo.name : 'Klik untuk unggah logo' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Body Template -->
        <div v-if="activeFormTab === 'body'" class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
          <!-- Placeholder Helper -->
          <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Variabel Placeholder Tersedia — Klik untuk Salin</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="ph in placeholders" :key="ph.key"
                @click="insertPlaceholder(ph.key)"
                class="px-3 py-1.5 bg-white dark:bg-slate-700 rounded-xl text-xs font-bold text-primary border border-primary/20 hover:bg-primary hover:text-white transition-all"
                :title="ph.desc"
              >
                {{ ph.key }}
              </button>
            </div>
          </div>
          <div class="space-y-1">
            <label class="label-field">Isi/Body Template</label>
            <textarea
              ref="bodyRef"
              v-model="form.body_template"
              class="input-field font-mono text-sm"
              style="min-height: 280px;"
              placeholder="Dengan hormat,

Bersama surat ini kami mengundang Bapak/Ibu {{tujuan_surat}} untuk hadir dalam:

Perihal : {{perihal}}
Tanggal : {{tanggal_surat}}

Demikian surat undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami mengucapkan terima kasih."
            ></textarea>
            <p class="text-[10px] text-slate-400 ml-1">Gunakan variabel placeholder di atas untuk auto-fill saat surat dibuat dari template ini.</p>
          </div>
        </div>

        <!-- Tab: Footer / TTD -->
        <div v-if="activeFormTab === 'footer'" class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
          <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800">
            <p class="text-xs font-bold text-amber-600 dark:text-amber-400">
              ✍️ Data footer dan tanda tangan akan tampil di bagian bawah dokumen surat.
            </p>
          </div>
          <div class="space-y-1">
            <label class="label-field">Kota (untuk tanggal)</label>
            <input v-model="form.footer_kota" type="text" class="input-field" placeholder="Surabaya" />
          </div>
          <div class="space-y-1">
            <label class="label-field">Jabatan Penandatangan</label>
            <input v-model="form.footer_jabatan_ttd" type="text" class="input-field" placeholder="Direktur Utama" />
          </div>
          <div class="space-y-1">
            <label class="label-field">Nama Penandatangan</label>
            <input v-model="form.footer_nama_ttd" type="text" class="input-field" placeholder="Dr. H. Ahmad Farouk, M.Kes" />
          </div>
          <div class="space-y-1">
            <label class="label-field">NIP Penandatangan</label>
            <input v-model="form.footer_nip_ttd" type="text" class="input-field" placeholder="NIP. 197001012000031001" />
          </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/30">
          <button @click="closeModal" class="px-5 py-2.5 text-slate-500 font-bold text-sm rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Batal</button>
          <button @click="handleSubmit" :disabled="submitting" class="btn-primary flex items-center gap-2">
            <div v-if="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <SaveIcon v-else class="w-4 h-4" />
            {{ editMode ? 'Simpan Perubahan' : 'Simpan Template' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== MODAL PREVIEW ========== -->
    <div v-if="showPreview && previewTemplate" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" @click="showPreview = false"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-3xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <div>
            <h3 class="font-black text-slate-800 dark:text-white">Preview Template</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ previewTemplate.nama_template }}</p>
          </div>
          <button @click="showPreview = false" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
            <XIcon class="w-5 h-5" />
          </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
          <!-- Dokumen Preview -->
          <div class="border border-slate-200 rounded-2xl p-8 bg-white shadow-sm font-serif text-slate-800 space-y-6 min-h-[500px]">
            <!-- Kop -->
            <div class="border-b-4 border-slate-800 pb-4 text-center">
              <div class="text-xl font-black uppercase tracking-wide">{{ previewTemplate.kop_nama_instansi || 'NAMA INSTANSI' }}</div>
              <div class="text-sm mt-1">{{ previewTemplate.kop_alamat || 'Alamat Instansi' }}</div>
              <div class="text-xs text-slate-500 mt-1 flex items-center justify-center gap-4">
                <span v-if="previewTemplate.kop_telp">Telp: {{ previewTemplate.kop_telp }}</span>
                <span v-if="previewTemplate.kop_email">Email: {{ previewTemplate.kop_email }}</span>
              </div>
            </div>

            <!-- Nomor surat area -->
            <div class="space-y-1 text-sm" v-pre>
              <div class="grid grid-cols-3 gap-2">
                <span class="font-bold">Nomor</span><span>:</span><span class="text-primary font-bold">{{nomor_surat}}</span>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <span class="font-bold">Perihal</span><span>:</span><span>{{perihal}}</span>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <span class="font-bold">Kepada Yth.</span><span>:</span><span>{{tujuan_surat}}</span>
              </div>
            </div>

            <!-- Body -->
            <div class="text-sm leading-loose whitespace-pre-wrap text-slate-700 py-4">{{ previewTemplate.body_template || 'Isi/body template belum diisi.' }}</div>

            <!-- Footer -->
            <div class="text-sm mt-8">
              <div class="text-right">
                <p>{{ previewTemplate.footer_kota || 'Kota' }}, <span v-pre>{{tanggal_surat}}</span></p>
                <p class="mt-1">{{ previewTemplate.footer_jabatan_ttd || 'Jabatan' }}</p>
                <div class="h-16"></div>
                <p class="font-bold underline">{{ previewTemplate.footer_nama_ttd || 'Nama Penandatangan' }}</p>
                <p class="text-slate-500">{{ previewTemplate.footer_nip_ttd || 'NIP. XXXXXXXXXX' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import {
  PlusIcon, SearchIcon, EditIcon, TrashIcon, EyeIcon,
  ChevronLeftIcon, ChevronRightIcon, SaveIcon, XIcon,
  FileTextIcon, LayoutTemplateIcon, ImageIcon,
  FileIcon, AlignLeftIcon, FootprintsIcon, InfoIcon
} from 'lucide-vue-next';
import { debounce } from '@/utils/debounce';
import api from '@/api/axios';

const items = ref([]);
const loading = ref(false);
const submitting = ref(false);
const showModal = ref(false);
const showPreview = ref(false);
const previewTemplate = ref(null);
const editMode = ref(false);
const selectedId = ref(null);
const search = ref('');
const filterJenis = ref('');
const pagination = ref({});
const activeFormTab = ref('info');
const bodyRef = ref(null);

const formTabs = [
  { id: 'info',   name: 'Informasi Dasar', icon: InfoIcon },
  { id: 'kop',    name: 'Kop Surat',       icon: FileIcon },
  { id: 'body',   name: 'Body / Isi',      icon: AlignLeftIcon },
  { id: 'footer', name: 'Footer / TTD',    icon: FootprintsIcon },
];

const placeholders = [
  { key: '{{nomor_surat}}',   desc: 'Nomor surat otomatis' },
  { key: '{{tanggal_surat}}', desc: 'Tanggal surat' },
  { key: '{{tujuan_surat}}',  desc: 'Tujuan / penerima surat' },
  { key: '{{perihal}}',       desc: 'Perihal / subjek surat' },
  { key: '{{nama_instansi}}', desc: 'Nama instansi dari kop' },
  { key: '{{ttd_nama}}',      desc: 'Nama penandatangan' },
  { key: '{{ttd_jabatan}}',   desc: 'Jabatan penandatangan' },
  { key: '{{ttd_nip}}',       desc: 'NIP penandatangan' },
  { key: '{{kota_tanggal}}',  desc: 'Kota + tanggal untuk TTD' },
];

const form = reactive({
  nama_template: '',
  kode_template: '',
  deskripsi: '',
  kop_nama_instansi: '',
  kop_alamat: '',
  kop_telp: '',
  kop_email: '',
  kop_website: '',
  kop_logo: null,
  body_template: '',
  footer_kota: '',
  footer_jabatan_ttd: '',
  footer_nama_ttd: '',
  footer_nip_ttd: '',
  jenis_surat: 'umum',
  is_active: true,
});

const resetForm = () => {
  Object.assign(form, {
    nama_template: '', kode_template: '', deskripsi: '',
    kop_nama_instansi: '', kop_alamat: '', kop_telp: '', kop_email: '', kop_website: '', kop_logo: null,
    body_template: '', footer_kota: '', footer_jabatan_ttd: '', footer_nama_ttd: '', footer_nip_ttd: '',
    jenis_surat: 'umum', is_active: true,
  });
};

const jenisBadge = (jenis) => {
  if (jenis === 'keluar') return 'text-blue-500 bg-blue-50 dark:bg-blue-900/30';
  if (jenis === 'masuk') return 'text-green-500 bg-green-50 dark:bg-green-900/30';
  return 'text-slate-500 bg-slate-50 dark:bg-slate-800';
};

const fetchItems = async (page = 1) => {
  loading.value = true;
  try {
    const { data } = await api.get('/template-surat', {
      params: { page, search: search.value, jenis_surat: filterJenis.value || undefined, per_page: 9 }
    });
    items.value = data.data;
    pagination.value = data;
  } catch (err) {
    console.error('Fetch error', err);
  } finally {
    loading.value = false;
  }
};

const handleSearch = debounce(() => fetchItems(1), 500);

const handleLogoUpload = (e) => { form.kop_logo = e.target.files[0]; };

const insertPlaceholder = (key) => {
  // Insert placeholder ke dalam textarea body_template
  const textarea = bodyRef.value;
  if (textarea) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = form.body_template;
    form.body_template = text.substring(0, start) + key + text.substring(end);
    // Restore cursor position
    setTimeout(() => {
      textarea.focus();
      textarea.selectionStart = textarea.selectionEnd = start + key.length;
    }, 10);
  } else {
    form.body_template += key;
  }
};

const openModal = (item = null) => {
  activeFormTab.value = 'info';
  if (item) {
    editMode.value = true;
    selectedId.value = item.id;
    Object.assign(form, {
      nama_template: item.nama_template || '',
      kode_template: item.kode_template || '',
      deskripsi: item.deskripsi || '',
      kop_nama_instansi: item.kop_nama_instansi || '',
      kop_alamat: item.kop_alamat || '',
      kop_telp: item.kop_telp || '',
      kop_email: item.kop_email || '',
      kop_website: item.kop_website || '',
      kop_logo: null,
      body_template: item.body_template || '',
      footer_kota: item.footer_kota || '',
      footer_jabatan_ttd: item.footer_jabatan_ttd || '',
      footer_nama_ttd: item.footer_nama_ttd || '',
      footer_nip_ttd: item.footer_nip_ttd || '',
      jenis_surat: item.jenis_surat || 'umum',
      is_active: item.is_active !== false,
    });
  } else {
    editMode.value = false;
    selectedId.value = null;
    resetForm();
  }
  showModal.value = true;
};

const closeModal = () => { showModal.value = false; };

const openPreview = (item) => {
  previewTemplate.value = item;
  showPreview.value = true;
};

const handleSubmit = async () => {
  if (!form.nama_template.trim()) {
    alert('Nama template wajib diisi!');
    activeFormTab.value = 'info';
    return;
  }
  submitting.value = true;
  try {
    const formData = new FormData();
    Object.entries(form).forEach(([key, val]) => {
      if (key === 'kop_logo') {
        if (val) formData.append('kop_logo', val);
      } else if (val !== null && val !== undefined) {
        formData.append(key, typeof val === 'boolean' ? (val ? '1' : '0') : val);
      }
    });

    if (editMode.value) {
      await api.post(`/template-surat/${selectedId.value}?_method=PUT`, formData);
    } else {
      await api.post('/template-surat', formData);
    }
    closeModal();
    fetchItems();
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal menyimpan template.';
    alert(msg);
  } finally {
    submitting.value = false;
  }
};

const handleDelete = async (id) => {
  if (confirm('Hapus template ini? Surat yang menggunakan template ini tidak akan terpengaruh.')) {
    try {
      await api.delete(`/template-surat/${id}`);
      fetchItems();
    } catch (e) {
      alert('Gagal menghapus template.');
    }
  }
};

onMounted(() => fetchItems(1));
</script>

<style scoped>
.label-field {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1;
}
</style>
