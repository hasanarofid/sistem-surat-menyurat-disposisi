<template>
  <div class="space-y-12 pb-24">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Pengaturan Sistem</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Konfigurasi identitas aplikasi, preferensi tampilan, dan parameter sistem.</p>
      </div>
      <button @click="saveSettings" class="btn-primary group flex items-center gap-3">
        <SaveIcon class="w-5 h-5 transition-transform group-hover:scale-110" />
        <span>Simpan Perubahan</span>
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      <!-- Navigation Sidebar -->
      <div class="lg:col-span-3 space-y-2">
        <button 
          v-for="tab in tabs" :key="tab.id"
          @click="activeTab = tab.id"
          class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 group"
          :class="activeTab === tab.id ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-[1.02]' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
        >
          <component :is="tab.icon" class="w-5 h-5 transition-transform group-hover:scale-110" />
          <span class="text-sm font-black uppercase tracking-widest">{{ tab.name }}</span>
        </button>
      </div>

      <!-- Main Settings Content -->
      <div class="lg:col-span-9">
        <transition name="page-fade" mode="out-in">
          <!-- General Settings -->
          <div v-if="activeTab === 'general'" class="card space-y-8 bg-white dark:bg-slate-900 border-none shadow-premium">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div class="space-y-6">
                <div>
                  <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6">Identitas Aplikasi</h3>
                  <div class="space-y-4">
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Aplikasi</label>
                      <input v-model="settings.appName" type="text" class="input-field" placeholder="E-Office Digital">
                    </div>
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Instansi / Perusahaan</label>
                      <input v-model="settings.orgName" type="text" class="input-field" placeholder="RSUD Dr. Soetomo">
                    </div>
                  </div>
                </div>
              </div>

              <div class="space-y-6">
                <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6">Branding & Logo</h3>
                <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100/50 transition-all group cursor-pointer">
                   <div class="w-20 h-20 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                     <ImageIcon class="w-10 h-10" />
                   </div>
                   <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Klik untuk unggah logo</p>
                   <p class="text-[10px] text-slate-400 mt-2">PNG / SVG (Max 1MB)</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Notification Settings -->
          <div v-else-if="activeTab === 'notifications'" class="card space-y-8 bg-white dark:bg-slate-900 border-none shadow-premium">
            <div>
              <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6">Notifikasi Email</h3>
              <div class="space-y-4">
                <div v-for="notif in notifSettings" :key="notif.id" class="flex items-center justify-between p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm">
                      <BellIcon class="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <div class="text-sm font-black text-slate-800 dark:text-white">{{ notif.name }}</div>
                      <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ notif.desc }}</div>
                    </div>
                  </div>
                  <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full relative p-1 cursor-pointer">
                    <div class="w-4 h-4 bg-white rounded-full transition-all" :class="notif.active ? 'translate-x-6' : ''" @click="notif.active = !notif.active"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Settings -->
          <div v-else-if="activeTab === 'system'" class="card space-y-8 bg-white dark:bg-slate-900 border-none shadow-premium">
             <div>
              <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6">Parameter Sistem</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4">
                   <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Maksimum Ukuran File (MB)</label>
                      <input v-model="settings.maxFileSize" type="number" class="input-field">
                    </div>
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Bahasa Default</label>
                      <select v-model="settings.language" class="input-field">
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English (US)</option>
                      </select>
                    </div>
                </div>
              </div>
             </div>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import Alert from '@/utils/alert';
import { 
  SettingsIcon, 
  BellIcon, 
  ShieldIcon, 
  DatabaseIcon, 
  SaveIcon, 
  ImageIcon,
  LayoutIcon
} from 'lucide-vue-next';

const activeTab = ref('general');

const tabs = [
  { id: 'general', name: 'Umum', icon: SettingsIcon },
  { id: 'notifications', name: 'Notifikasi', icon: BellIcon },
  { id: 'system', name: 'Sistem', icon: DatabaseIcon },
  { id: 'appearance', name: 'Tampilan', icon: LayoutIcon },
];

const settings = reactive({
  appName: 'E-Office Digital',
  orgName: 'RSUD Dr. Soetomo',
  maxFileSize: 2,
  language: 'id',
});

const notifSettings = ref([
  { id: 1, name: 'Surat Masuk Baru', desc: 'Kirim notifikasi setiap ada surat baru masuk.', active: true },
  { id: 2, name: 'Disposisi Baru', desc: 'Kirim notifikasi saat menerima instruksi baru.', active: true },
  { id: 3, name: 'Update Status Surat', desc: 'Notifikasi perubahan status dari proses ke archive.', active: false },
]);

const saveSettings = () => {
    // Show success toast logic here
    Alert.success('Konfigurasi sistem berhasil diperbarui.');
};
</script>

<style scoped>
.page-fade-enter-active,
.page-fade-leave-active {
  transition: all 0.3s ease;
}

.page-fade-enter-from,
.page-fade-leave-to {
  opacity: 0;
  transform: translateX(10px);
}
</style>
