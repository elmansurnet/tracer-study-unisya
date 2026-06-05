<template>
  <section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-600 dark:text-primary-300">
        Portal Employer
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Akses kuesioner employer
      </h1>
      <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
        Masukkan token undangan yang diterima dari alumni untuk melanjutkan ke proses verifikasi OTP dan pengisian kuesioner.
      </p>

      <form class="mt-8 space-y-5" @submit.prevent="submitToken">
        <AppInput
          v-model="token"
          label="Token Undangan"
          placeholder="Masukkan token akses employer"
          :error="error"
          hint="Token bersifat unik dan memiliki masa berlaku terbatas."
          required
        />

        <div class="flex flex-col gap-3 sm:flex-row">
          <AppButton type="submit" :loading="loading">
            Lanjutkan Verifikasi
          </AppButton>
          <AppButton variant="secondary" @click="$router.push('/login')">
            Kembali ke Login
          </AppButton>
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/base/AppButton.vue'
import AppInput from '@/components/base/AppInput.vue'
import { useUIStore } from '@/stores/ui'

const router = useRouter()
const ui = useUIStore()

const token = ref('')
const error = ref('')
const loading = ref(false)

function submitToken() {
  error.value = ''

  if (!token.value.trim()) {
    error.value = 'Token undangan wajib diisi.'
    return
  }

  loading.value = true

  setTimeout(() => {
    loading.value = false
    router.push({
      name: 'employer-otp',
      query: { token: token.value.trim() },
    })
  }, 500)
}

onMounted(() => {
  ui.setPageTitle('Akses Employer')
  ui.setBreadcrumbs([{ label: 'Employer' }, { label: 'Akses' }])
})
</script>