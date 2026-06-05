<template>
  <section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-600 dark:text-primary-300">
        Verifikasi OTP
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Masukkan kode OTP employer
      </h1>
      <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
        OTP dikirim ke kontak employer yang terdaftar pada undangan. Gunakan kode tersebut untuk mengakses dashboard employer.
      </p>

      <form class="mt-8 space-y-5" @submit.prevent="verifyOtp">
        <AppInput
          v-model="otp"
          label="Kode OTP"
          placeholder="Contoh: 123456"
          :error="error"
          hint="Kode OTP terdiri dari 6 digit."
          required
        />

        <div class="flex flex-col gap-3 sm:flex-row">
          <AppButton type="submit" :loading="loading">
            Verifikasi OTP
          </AppButton>
          <AppButton variant="ghost" @click="$router.push('/employer/akses')">
            Kembali
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

const otp = ref('')
const error = ref('')
const loading = ref(false)

function verifyOtp() {
  error.value = ''

  if (!/^\d{6}$/.test(otp.value.trim())) {
    error.value = 'Kode OTP harus terdiri dari 6 digit angka.'
    return
  }

  loading.value = true

  setTimeout(() => {
    loading.value = false
    router.push('/employer/dashboard')
  }, 500)
}

onMounted(() => {
  ui.setPageTitle('Verifikasi OTP Employer')
  ui.setBreadcrumbs([{ label: 'Employer' }, { label: 'Verifikasi OTP' }])
})
</script>