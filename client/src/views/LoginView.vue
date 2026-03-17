<script setup path="client/src/views/LoginView.vue">
import { ref, reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
  email: '',
  password: '',
});

// Menampung error validasi per field (email, password)
const errors = ref<Record<string, string[]>>({});
// Menampung error umum (misal: "Email not found")
const errorMessage = ref('');

const handleLogin = async () => {
  errors.value = {};
  errorMessage.value = '';
  
  try {
    await auth.login(form);
    // Redirect berdasarkan role setelah login berhasil
    if (auth.isAdmin) {
      router.push('/admin');
    } else {
      router.push('/dashboard');
    }
  } catch (err: any) {
    // Tangkap error validasi (422) atau error umum
    if (err.errors) {
      errors.value = err.errors;
    } else {
      errorMessage.value = err.message || 'Login gagal. Cek koneksi API.';
    }
  }
};
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-[#1A1D23] px-4 font-sans">
    
    <h1 class="text-xs font-bold text-white mb-3 tracking-wider">LOGIN</h1>

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-2xl p-12 border-4 border-[#1A1D23]">
      
      <div class="mb-12">
        <h2 class="text-4xl font-extrabold text-[#1A1D23] flex items-center">
          Leave<span class="text-[#3B82F6]">Hub</span>
        </h2>
        <p class="text-xl text-[#6B7280] mt-3 font-medium">Leave Request Management System</p>
      </div>

      <div v-if="errorMessage" class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm rounded-lg">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-8">
        <div>
          <label class="block text-xl font-bold text-[#4B5563] mb-3">Email</label>
          <input 
            v-model="form.email"
            type="email" 
            class="w-full px-6 py-4 border-2 rounded-xl text-lg focus:ring-4 focus:ring-blue-200 outline-none transition placeholder-[#9CA3AF]"
            :class="errors.email ? 'border-red-500' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
            placeholder="admin@energeek.id"
          />
          <p v-if="errors.email" class="mt-2 text-sm text-red-500 font-medium">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label class="block text-xl font-bold text-[#4B5563] mb-3">Password</label>
          <input 
            v-model="form.password"
            type="password" 
            class="w-full px-6 py-4 border-2 rounded-xl text-lg focus:ring-4 focus:ring-blue-200 outline-none transition placeholder-[#9CA3AF]"
            :class="errors.password ? 'border-red-500' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
            placeholder="••••••••••••"
          />
          <p v-if="errors.password" class="mt-2 text-sm text-red-500 font-medium">{{ errors.password[0] }}</p>
        </div>

        <button 
          type="submit" 
          :disabled="auth.loading"
          class="w-full bg-[#3B82F6] hover:bg-blue-600 text-white font-bold text-2xl py-5 rounded-2xl transition duration-200 disabled:opacity-50"
        >
          <span v-if="auth.loading">Authenticating...</span>
          <span v-else>Login</span>
        </button>
      </form>

      <div class="text-center mt-12">
        <p class="text-lg text-[#6B7280] font-medium">
          Sanctum PAT · No register endpoint
        </p>
      </div>
    </div>
  </div>
</template>