import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/store/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: () => import('@/pages/LoginPage.vue'),
            meta: { guest: true },
        },
        {
            path: '/',
            component: () => import('@/layouts/DashboardLayout.vue'),
            meta: { auth: true },
            children: [
                {
                    path: '',
                    name: 'dashboard',
                    component: () => import('@/pages/DashboardPage.vue'),
                },
                {
                    path: 'surat-masuk',
                    name: 'surat-masuk',
                    component: () => import('@/pages/SuratMasukPage.vue'),
                },
                {
                    path: 'surat-keluar',
                    name: 'surat-keluar',
                    component: () => import('@/pages/SuratKeluarPage.vue'),
                },
                {
                    path: 'disposisi',
                    name: 'disposisi',
                    component: () => import('@/pages/DisposisiIndexPage.vue'),
                },
                {
                    path: 'disposisi/:id',
                    name: 'disposisi-detail',
                    component: () => import('@/pages/DisposisiPage.vue'),
                },
                {
                    path: 'arsip',
                    name: 'arsip',
                    component: () => import('@/pages/ArsipPage.vue'),
                },
                {
                    path: 'template-surat',
                    name: 'template-surat',
                    component: () => import('@/pages/TemplateSuratPage.vue'),
                },
                {
                    path: 'users',
                    name: 'users',
                    component: () => import('@/pages/UserManagementPage.vue'),
                },
                {
                    path: 'setting',
                    name: 'setting',
                    component: () => import('@/pages/SettingPage.vue'),
                },
            ],
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();
    
    if (to.meta.auth && !auth.isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && auth.isAuthenticated) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;
