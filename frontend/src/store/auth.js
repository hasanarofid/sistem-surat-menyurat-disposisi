import { defineStore } from 'pinia';
import api from '@/api/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const { data } = await api.post('/auth/login', {
                    ...credentials,
                    device_name: 'webapp',
                });
                this.user = data.user;
                this.token = data.token;
                localStorage.setItem('token', data.token);
                return data;
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                await api.post('/auth/logout');
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
            }
        },
        async fetchUser() {
            try {
                const { data } = await api.get('/auth/me');
                this.user = data;
            } catch (error) {
                this.logout();
            }
        },
    },
});
