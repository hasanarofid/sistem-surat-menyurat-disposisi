<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop with deeper blur -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" @click="$emit('close')"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] w-full max-w-2xl overflow-hidden transform transition-all border border-slate-100 dark:border-slate-800">
      <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/20">
        <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">{{ title }}</h3>
        <button @click="$emit('close')" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
        <slot />
      </div>

      <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex justify-end gap-3">
        <button @click="$emit('close')" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
          Cancel
        </button>
        <slot name="footer">
          <button @click="$emit('submit')" class="btn-primary min-w-[140px]">
            Save Changes
          </button>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Modal Title'
    }
});

defineEmits(['close', 'submit']);
</script>
