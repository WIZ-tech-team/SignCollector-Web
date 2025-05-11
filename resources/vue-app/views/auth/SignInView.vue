<template>
  <div class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
      <!-- Header -->
      <h1 class="text-4xl font-bold text-center text-gray-900 mb-2">
        حصر لوائح الطرق
      </h1>
      <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">
        لوحة التحكم
      </h2>

      <!-- Greeting -->
      <p class="text-center text-gray-600 mb-8">مرحبًا بك!</p>

      <!-- Form -->
      <form @submit.prevent="simpleLogin" class="space-y-6">
        <!-- Username -->
        <div>
          <label class="block text-lg font-medium text-gray-800 mb-1">
            اسم المستخدم
          </label>
          <input
            v-model="name"
            type="text"
            autocomplete="username"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="أدخل اسم المستخدم"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-lg font-medium text-gray-800 mb-1">
            كلمة السر
          </label>
          <input
            v-model="password"
            type="password"
            autocomplete="current-password"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="أدخل كلمة السر"
          />
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-md disabled:opacity-50 transition"
        >
          <span v-if="!loading">دخول</span>
          <span v-else>جاري التحميل…</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/store/stores/authStore';
import ApiService from '@/core/services/ApiService';

const name     = ref('');
const password = ref('');
const loading  = ref(false);
const router   = useRouter();
const authStore = useAuthStore();

async function simpleLogin() {
  loading.value = true;
  try {
    // 1) Hit the login endpoint
    const res = await axios.post(
      'http://206.189.62.53:8070/api/spa/admin/auth/login',
      { name: name.value, password: password.value },
      { headers: { Accept: 'application/json' } }
    );

    const { token, user } = res.data.data;

    // 2) Persist token & set axios default header
    localStorage.setItem('token', token);
    ApiService.setHeader(token);

    // 3) Update Pinia authStore
    authStore.token = token;
    authStore.user  = user;
    authStore.authenticate(); // sets isAuthenticated = true

    // 4) Redirect to dashboard
    await router.push('/dashboardLayout');
  } catch (e: any) {
    alert(e.response?.data?.message || 'فشل في تسجيل الدخول');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
/* no extra CSS—Tailwind handles it */
</style>
