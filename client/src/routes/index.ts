import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import LoginView from '@/views/LoginView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { guestOnly: true }
    },
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      // Menggunakan Lazy Loading agar lebih ringan
      component: () => import('@/views/dashboard/UserDashboard.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/admin',
      name: 'admin-dashboard',
      component: () => import('@/views/dashboard/AdminDashboard.vue'),
      meta: { requiresAuth: true, adminOnly: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/errors/NotFound.vue')
    }
  ]
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  const isAuthenticated = auth.isAuthenticated;
  const isAdmin = auth.isAdmin;

  // 1. Jika halaman butuh login tapi user belum login
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login' });
  }

  // 2. Jika halaman khusus admin tapi user bukan admin
  if (to.meta.adminOnly && !isAdmin) {
    return next({ name: 'dashboard' });
  }

  // 3. Jika user sudah login tapi mencoba ke halaman login
  if (to.meta.guestOnly && isAuthenticated) {
    return next({ name: 'dashboard' });
  }

  next();
});

export default router;