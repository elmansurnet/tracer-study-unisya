<template>
  <section class="mx-auto flex min-h-[60vh] max-w-3xl items-center justify-center">
    <div class="w-full rounded-[28px] border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-10">
      <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-3xl dark:bg-slate-800">
        404
      </div>

      <p class="mt-6 text-sm font-semibold uppercase tracking-[0.2em] text-primary-600 dark:text-primary-300">
        Halaman Tidak Ditemukan
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Tautan yang Anda buka tidak tersedia
      </h1>
      <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-400">
        Periksa kembali alamat halaman atau kembali ke area utama sistem untuk melanjutkan pekerjaan Anda.
      </p>

      <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
        <AppButton @click="$router.back()">
          Kembali
        </AppButton>
        <AppButton variant="secondary" @click="$router.push('/login')">
          Ke Login
        </AppButton>
      </div>
    </div>
  </section>
</template>

<script setup>
import AppButton from '@/components/base/AppButton.vue'
</script>