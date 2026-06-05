<template>
  <div class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto flex max-w-6xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:min-h-[720px] lg:flex-row">
      <section class="flex flex-1 flex-col justify-between bg-primary-600 p-8 text-white lg:p-12">
        <div>
          <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-100">
            Tracer Study UNISYA
          </p>
          <h1 class="mt-6 font-display text-4xl font-bold leading-tight lg:text-5xl">
            Masuk ke portal tracer study yang aman dan terintegrasi.
          </h1>
          <p class="mt-4 max-w-xl text-base leading-7 text-primary-50/90">
            Gunakan email dan kata sandi, atau minta OTP melalui email maupun WhatsApp sesuai metode akses yang tersedia.
          </p>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
          <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
            <p class="text-2xl font-bold">1</p>
            <p class="mt-1 text-sm text-primary-50">Login admin dan alumni dalam satu portal.</p>
          </div>
          <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
            <p class="text-2xl font-bold">2</p>
            <p class="mt-1 text-sm text-primary-50">OTP 6 digit dengan masa berlaku terbatas.</p>
          </div>
          <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
            <p class="text-2xl font-bold">3</p>
            <p class="mt-1 text-sm text-primary-50">State autentikasi dipulihkan otomatis saat refresh.</p>
          </div>
        </div>
      </section>

      <section class="flex w-full flex-1 items-center justify-center p-6 lg:p-10">
        <div class="w-full max-w-lg">
          <div class="mb-6">
            <h2 class="text-3xl font-bold text-slate-900">Masuk</h2>
            <p class="mt-2 text-sm text-slate-500">
              Pilih metode login yang sesuai untuk akun Anda.
            </p>
          </div>

          <div class="mb-6 inline-flex rounded-2xl bg-slate-100 p-1">
            <button
              type="button"
              class="rounded-xl px-4 py-2 text-sm font-semibold transition"
              :class="tab === 'password' ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-600'"
              @click="tab = 'password'"
            >
              Email & Kata Sandi
            </button>
            <button
              type="button"
              class="rounded-xl px-4 py-2 text-sm font-semibold transition"
              :class="tab === 'otp' ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-600'"
              @click="tab = 'otp'"
            >
              OTP
            </button>
          </div>

          <div
            v-if="flashMessage"
            class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
          >
            {{ flashMessage }}
          </div>

          <div
            v-if="errorMessage"
            class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
          >
            {{ errorMessage }}
          </div>

          <form v-if="tab === 'password'" class="space-y-5" @submit.prevent="submitPasswordLogin">
            <div>
              <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
                Email
              </label>
              <input
                id="email"
                v-model.trim="loginForm.email"
                type="email"
                autocomplete="username"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                placeholder="nama@contoh.ac.id"
              />
              <p v-if="fieldErrors.email" class="mt-2 text-sm text-rose-600">
                {{ fieldErrors.email }}
              </p>
            </div>

            <div>
              <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                Kata Sandi
              </label>
              <input
                id="password"
                v-model="loginForm.password"
                type="password"
                autocomplete="current-password"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                placeholder="Masukkan kata sandi"
              />
              <p v-if="fieldErrors.password" class="mt-2 text-sm text-rose-600">
                {{ fieldErrors.password }}
              </p>
            </div>

            <button
              type="submit"
              class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70"
              :disabled="auth.authLoading"
            >
              {{ auth.authLoading ? 'Memproses...' : 'Masuk Sekarang' }}
            </button>
          </form>

          <form v-else class="space-y-5" @submit.prevent="submitOtpRequest">
            <div>
              <label for="identifier" class="mb-2 block text-sm font-medium text-slate-700">
                Email / Nomor WhatsApp
              </label>
              <input
                id="identifier"
                v-model.trim="otpRequestForm.identifier"
                type="text"
                autocomplete="username"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                placeholder="email@contoh.ac.id atau 628xxxxxxxxxx"
              />
              <p v-if="fieldErrors.identifier" class="mt-2 text-sm text-rose-600">
                {{ fieldErrors.identifier }}
              </p>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Tipe Identitas
              </label>
              <div class="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  class="rounded-2xl border px-4 py-3 text-sm font-medium transition"
                  :class="otpRequestForm.identifierType === 'email'
                    ? 'border-primary-500 bg-primary-50 text-primary-700'
                    : 'border-slate-300 text-slate-600 hover:border-primary-300'"
                  @click="otpRequestForm.identifierType = 'email'"
                >
                  Email
                </button>
                <button
                  type="button"
                  class="rounded-2xl border px-4 py-3 text-sm font-medium transition"
                  :class="otpRequestForm.identifierType === 'whatsapp'
                    ? 'border-primary-500 bg-primary-50 text-primary-700'
                    : 'border-slate-300 text-slate-600 hover:border-primary-300'"
                  @click="otpRequestForm.identifierType = 'whatsapp'"
                >
                  WhatsApp
                </button>
              </div>
            </div>

            <button
              type="submit"
              class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70"
              :disabled="auth.authLoading"
            >
              {{ auth.authLoading ? 'Mengirim OTP...' : 'Kirim OTP' }}
            </button>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const tab = ref('password')
const errorMessage = ref('')
const flashMessage = ref('')

const loginForm = reactive({
  email: '',
  password: '',
})

const otpRequestForm = reactive({
  identifier: auth.otpContext.identifier || '',
  identifierType: auth.otpContext.identifierType || 'email',
})

const fieldErrors = reactive({
  email: '',
  password: '',
  identifier: '',
})

function resetErrors() {
  errorMessage.value = ''
  flashMessage.value = ''
  fieldErrors.email = ''
  fieldErrors.password = ''
  fieldErrors.identifier = ''
}

function resolveRedirectByRole(role) {
  const redirectMap = {
    super_admin: '/admin/dashboard',
    alumni: '/alumni/dashboard',
  }

  return redirectMap[role] ?? '/login'
}

async function submitPasswordLogin() {
  resetErrors()

  if (!loginForm.email) {
    fieldErrors.email = 'Email wajib diisi.'
  }

  if (!loginForm.password) {
    fieldErrors.password = 'Kata sandi wajib diisi.'
  }

  if (fieldErrors.email || fieldErrors.password) {
    return
  }

  try {
    await auth.login({
      email: loginForm.email,
      password: loginForm.password,
    })

    await router.replace(resolveRedirectByRole(auth.user?.role))
  } catch (error) {
    errorMessage.value = error.response?.data?.message ?? 'Login gagal. Periksa kembali kredensial Anda.'
  }
}

async function submitOtpRequest() {
  resetErrors()

  if (!otpRequestForm.identifier) {
    fieldErrors.identifier = 'Email atau nomor WhatsApp wajib diisi.'
    return
  }

  try {
    const response = await auth.requestOtp({
      identifier: otpRequestForm.identifier,
      identifierType: otpRequestForm.identifierType,
    })

    flashMessage.value = response?.message ?? 'OTP berhasil dikirim.'

    await router.push({
      name: 'login-otp',
      query: {
        identifier: otpRequestForm.identifier,
        identifier_type: otpRequestForm.identifierType,
      },
    })
  } catch (error) {
    errorMessage.value = error.response?.data?.message ?? 'Permintaan OTP gagal diproses.'
  }
}
</script>