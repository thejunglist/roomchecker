import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('auth_token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', { email, password });
        this.user = response.data.user;
        this.token = response.data.token;
        
        localStorage.setItem('user', JSON.stringify(response.data.user));
        localStorage.setItem('auth_token', response.data.token);
        
        return true;
      } catch (error) {
        throw error;
      }
    },

    async logout() {
      try {
        await api.post('/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('user');
        localStorage.removeItem('auth_token');
      }
    },
  },
});