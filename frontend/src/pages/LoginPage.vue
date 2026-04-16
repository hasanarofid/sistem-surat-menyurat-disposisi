<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
    <div class="max-w-md w-full">
      <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-white mb-2">
          <span class="text-blue-500">BLU</span> E-Office
        </h1>
        <p class="text-slate-400">Sign in to manage your documents</p>
      </div>
      
      <div class="card p-8 bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="w-full px-4 py-3 bg-slate-800 border border-slate-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500"
              placeholder="name@blu.com"
              required
            >
          </div>
          <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
            <div class="relative group">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500 transition-all focus:shadow-glow pr-12"
                placeholder="••••••••"
                required
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-all h-10 w-10 flex items-center justify-center rounded-xl hover:bg-white/5"
              >
                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                <EyeOffIcon v-else class="w-5 h-5" />
              </button>
            </div>
          </div>
          
          <div v-if="error" class="p-3 bg-red-400/10 border border-red-400/20 text-red-400 text-sm rounded-lg text-center">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="auth.loading"
            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 disabled:bg-blue-800 text-white font-bold rounded-xl shadow-lg transition-all duration-200"
          >
            {{ auth.loading ? 'Signing in...' : 'Sign In' }}
          </button>
        </form>
      </div>
      
      <p class="text-center mt-8 text-slate-500 text-sm">
        &copy; 2026 BLU Labs. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/store/auth';
import { useRouter } from 'vue-router';
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next';
import Alert from '@/utils/alert';

const auth = useAuthStore();
const router = useRouter();
const showPassword = ref(false);

const form = reactive({
    email: '',
    password: '',
});

const error = ref(null);

const handleLogin = async () => {
    error.value = null;
    try {
        await auth.login(form);
        Alert.success('Selamat datang kembali di E-Office.');
        router.push('/');
    } catch (err) {
        const msg = err.response?.data?.message || 'Login failed. Please check your credentials.';
        error.value = msg;
        Alert.error(msg, 'Login Gagal');
    }
};
</script>
