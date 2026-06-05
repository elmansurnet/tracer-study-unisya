<template>
  <div class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto flex max-w-5xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:min-h-[640px] lg:flex-row">
      <section class="flex flex-1 flex-col justify-between bg-slate-900 p-8 text-white lg:p-12">
        <div>
          <p class="text-sm font-medium uppercase tracking-[0.24em] text-slate-300">
            Verifikasi OTP
          </p>
          <h1 class="mt-6 font-display text-4xl font-bold leading-tight">
            Masukkan kode OTP 6 digit untuk melanjutkan.
          </h1>
          <p class="mt-4 max-w-xl text-base leading-7 text-slate-300">
            OTP memiliki masa berlaku terbatas. Pastikan kode dimasukkan sebelum waktu habis.
          </p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
          <p class="text-sm text-slate-300">Identitas tujuan OTP</p>
          <p class="mt-2 break-all text-lg font-semibold text-white">{{ identifier }}</p>
          <p class="mt-2 text-sm text-slate-400">
            Metode: {{ identifierTypeLabel }}
          </p>
        </div>
      </section>

      <section class="flex flex-1 items-center justify-center p-6 lg:p-10">
        <div class="w-full max-w-md">
          <div class="mb-6">
            <h2 class="text-3xl font-bold text-slate-900">Verifikasi OTP</h2>
            <p class="mt-2 text-sm text-slate-500">
              Masukkan kode 6 digit yang telah dikirim.
            </p>
          </div>

          <div
            v-if="successMessage"
            class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
          >
            {{ successMessage }}
          </div>

          <div
            v-if="errorMessage"
            class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
          >
            {{ errorMessage }}
          </div>

          <form class="space-y-5" @submit.prevent="submitOtpVerification">
            <div>
              <label for="otp" class="mb-2 block text-sm font-medium text-slate-700">
                Kode OTP
              </label>
              <input
                id="otp"
                v-model="otpCode"
                type="text"
                inputmode="numeric"
                maxlength="6"
                class="w-full rounded-2xl border border-slate-300 px-4 py-4 text-center font-mono text-2xl tracking-[0.45em] shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                placeholder="000000"
                @input="sanitizeOtp"
              />
              <p class="mt-2 text-sm text-slate-500">
                Sisa waktu: <span class="font-semibold text-slate-800">{{ formattedCountdown }}</span>
              </p>
            </div>

            <button
              type="submit"
              class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70"
              :disabled="auth.authLoading || otpCode.length !== 6 || countdown <= 0"
            >
              {{ auth.authLoading ? 'Memverifikasi...' : 'Verifikasi OTP' }}
            </button>

            <button
              type="button"
              class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-primary-300 hover:text-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="auth.authLoading || resendLoading || countdown > 0"
              @click="resendOtp"
            >
              {{ resendLoading ? 'Mengirim ulang...' : 'Kirim Ulang OTP' }}
            </button>

            <button
              type="button"
              class="inline-flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:text-slate-700"
              @click="router.push({ name: 'login' })"
            >
              Kembali ke halaman login
            </button>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const otpCode = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const resendLoading = ref(false)
const countdown = ref(0)

let timerId = null

const identifier = computed(() => route.query.identifier || auth.otpContext.identifier || '')
const identifierType = computed(() => route.query.identifier_type || auth.otpContext.identifierType || 'email')

const identifierTypeLabel = computed(() => {
  return identifierType.value === 'whatsapp' ? 'WhatsApp' : 'Email'
})

const formattedCountdown = computed(() => {
  const minutes = Math.floor(countdown.value / 60)
  const seconds = countdown.value % 60
  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

function startCountdown(seconds) {
  clearCountdown()
  countdown.value = seconds

  timerId = window.setInterval(() => {
    if (countdown.value > 0) {
      countdown.value -= 1
      return
    }

    clearCountdown()
  }, 1000)
}

function clearCountdown() {
  if (timerId) {
    window.clearInterval(timerId)
    timerId = null
  }
}

function sanitizeOtp() {
  otpCode.value = otpCode.value.replace(/\D/g, '').slice(0, 6)
}

function resetMessages() {
  errorMessage.value = ''
  successMessage.value = ''
}

function resolveRedirectByRole(role) {
  const redirectMap = {
    super_admin: '/admin/dashboard',
    alumni: '/alumni/dashboard',
  }

  return redirectMap[role] ?? '/login'
}

async function submitOtpVerification() {
  resetMessages()

  if (!identifier.value) {
    errorMessage.value = 'Identitas OTP tidak ditemukan. Silakan ulangi permintaan OTP dari halaman login.'
    return
  }

  if (otpCode.value.length !== 6) {
    errorMessage.value = 'Kode OTP harus terdiri dari 6 digit.'
    return
  }

  try {
    await auth.verifyOtp({
      identifier: identifier.value,
      otpCode: otpCode.value,
    })

    await router.replace(resolveRedirectByRole(auth.user?.role))
  } catch (error) {
    errorMessage.value = error.response?.data?.message ?? 'Verifikasi OTP gagal. Silakan coba kembali.'
  }
}

async function resendOtp() {
  resetMessages()

  if (!identifier.value) {
    errorMessage.value = 'Identitas OTP tidak tersedia untuk pengiriman ulang.'
    return
  }

  resendLoading.value = true

  try {
    const response = await auth.requestOtp({
      identifier: identifier.value,
      identifierType: identifierType.value,
    })

    successMessage.value = response?.message ?? 'OTP berhasil dikirim ulang.'
    otpCode.value = ''
    startCountdown(Number(response?.data?.expires_in ?? 300))
  } catch (error) {
    errorMessage.value = error.response?.data?.message ?? 'Gagal mengirim ulang OTP.'
  } finally {
    resendLoading.value = false
  }
}

onMounted(() => {
  const fallbackSeconds = Number(auth.otpContext.expiresIn || 300)
  startCountdown(fallbackSeconds)
})

onBeforeUnmount(() => {
  clearCountdown()
})
</script>