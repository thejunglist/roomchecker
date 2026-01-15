import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/',
    name: 'Rooms',
    component: () => import('../views/RoomsView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/rooms/:id',
    name: 'RoomDetail',
    component: () => import('../views/RoomDetailView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/sessions/:id',
    name: 'MaintenanceSession',
    component: () => import('../views/MaintenanceSessionView.vue'),
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Auth guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if (to.path === '/login' && authStore.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;