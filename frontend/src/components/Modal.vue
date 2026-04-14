<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="$emit('close')"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-xl font-bold text-slate-800">{{ title }}</h3>
        <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="p-6 max-h-[70vh] overflow-y-auto">
        <slot />
      </div>

      <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3">
        <button @click="$emit('close')" class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">
          Cancel
        </button>
        <slot name="footer">
          <button @click="$emit('submit')" class="btn-primary">
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
