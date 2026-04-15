<template>
  <div class="relative searchable-select" v-click-outside="close">
    <div 
      @click="toggle"
      class="input-field cursor-pointer flex items-center justify-between group h-12"
      :class="{ 'border-primary ring-2 ring-primary/20': isOpen }"
    >
      <div v-if="selected" class="flex items-center gap-2">
        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors group-hover:text-primary">{{ selectedLabel }}</span>
      </div>
      <span v-else class="text-sm text-slate-400 font-medium">{{ placeholder }}</span>
      <ChevronDownIcon class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180 text-primary': isOpen }" />
    </div>

    <transition name="pop">
      <div v-if="isOpen" class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-3 border-b border-slate-50 dark:border-slate-800">
          <div class="flex items-center bg-slate-50 dark:bg-slate-800/80 rounded-full px-4 py-2.5 group transition-all duration-300 border border-slate-100 dark:border-slate-700/50 focus-within:border-accent-blue focus-within:shadow-[0_0_15px_-2px_rgba(59,130,246,0.4)] ring-4 ring-transparent focus-within:ring-accent-blue/10">
            <div class="relative flex items-center justify-center">
              <SearchIcon class="w-4 h-4 text-slate-400 group-focus-within:text-accent-blue transition-all duration-300" />
              <div class="absolute inset-0 bg-accent-blue/20 blur-lg opacity-0 group-focus-within:opacity-100 animate-pulse transition-opacity duration-500"></div>
            </div>
            <input 
              ref="searchInput"
              v-model="search" 
              type="text" 
              placeholder="Cari..." 
              class="bg-transparent border-none focus:ring-0 focus:outline-none text-sm ml-3 w-full font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400/50 tracking-tight"
              @keyup.enter="selectFirst"
            />
          </div>
        </div>
        <div class="max-h-60 overflow-y-auto py-2">
          <div 
            v-for="option in filteredOptions" 
            :key="option.id"
            @click="select(option)"
            class="px-5 py-3 text-sm font-bold cursor-pointer transition-all flex items-center justify-between"
            :class="modelValue === option.id ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'"
          >
            <span>{{ option.name }}</span>
            <CheckIcon v-if="modelValue === option.id" class="w-4 h-4" />
          </div>
          <div v-if="filteredOptions.length === 0" class="px-5 py-8 text-center text-slate-400 italic text-sm font-medium">
            Tidak ditemukan hasil "{{ search }}"
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { SearchIcon, ChevronDownIcon, CheckIcon } from 'lucide-vue-next';

const props = defineProps({
  modelValue: [String, Number],
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Pilih opsi...' },
  labelKey: { type: String, default: 'name' }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const search = ref('');
const searchInput = ref(null);

const filteredOptions = computed(() => {
  if (!search.value) return props.options;
  return props.options.filter(opt => 
    opt[props.labelKey].toLowerCase().includes(search.value.toLowerCase())
  );
});

const selected = computed(() => props.options.find(opt => opt.id === props.modelValue));
const selectedLabel = computed(() => selected.value ? selected.value[props.labelKey] : '');

const toggle = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    search.value = '';
    nextTick(() => searchInput.value?.focus());
  }
};

const select = (option) => {
  emit('update:modelValue', option.id);
  emit('change', option);
  isOpen.value = false;
};

const selectFirst = () => {
  if (filteredOptions.value.length > 0) {
    select(filteredOptions.value[0]);
  }
};

const close = () => {
  isOpen.value = false;
};

// Custom directive for clicking outside
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value();
      }
    };
    document.addEventListener("click", el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener("click", el.clickOutsideEvent);
  },
};
</script>

<style scoped>
.pop-enter-active, .pop-leave-active {
  transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.pop-enter-from, .pop-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.98);
}

.searchable-select ::-webkit-scrollbar { width: 5px; }
.searchable-select ::-webkit-scrollbar-thumb { 
  background-color: rgb(226, 232, 240); 
  border-radius: 9999px; 
}
.dark .searchable-select ::-webkit-scrollbar-thumb { 
  background-color: rgb(51, 65, 85); 
}
</style>
