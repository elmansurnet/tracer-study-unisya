<template>
  <section class="mx-auto flex min-h-[60vh] max-w-3xl items-center justify-center">
    <div class="w-full rounded-[28px] border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-10">
      <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-3xl dark:bg-amber-500/20">
        🔒
      </div>

      <p class="mt-6 text-sm font-semibold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">
        Akses Ditolak
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Anda tidak memiliki izin untuk membuka halaman ini
      </h1>
      <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-400">
        Sistem membatasi akses berdasarkan peran pengguna. Silakan kembali ke halaman yang sesuai dengan akun Anda.
      </p>

      <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
        <AppButton @click="$router.back()">
          Kembali
        </AppButton>
        <AppButton variant="secondary" @click="goHome">
          Ke Dashboard
        </AppButton>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from 'vue-router'
import AppButton from '@/components/base/AppButton.vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

function goHome() {
  if (auth.user?.role === 'super_admin') {
    router.push('/admin/dashboard')
    return
  }

  if (auth.user?.role === 'alumni') {
    router.push('/alumni/dashboard')
    return
  }

  router.push('/login')
}
</script>