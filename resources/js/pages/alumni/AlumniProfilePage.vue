<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Profil Saya</h1>
      <p class="mt-1 text-sm text-gray-500">Lihat dan perbarui informasi kontak Anda</p>
    </div>

    <!-- Loading -->
    <div v-if="store.loading && !store.currentAlumni" class="space-y-4">
      <div class="h-40 animate-pulse rounded-xl bg-gray-100" />
      <div class="h-64 animate-pulse rounded-xl bg-gray-100" />
    </div>

    <template v-else-if="store.currentAlumni">
      <!-- Profil Card -->
      <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="h-16 bg-gradient-to-r from-primary-500 to-primary-300" />
        <div class="px-6 pb-6">
          <div class="flex items-end gap-4 -mt-8">
            <!-- Avatar -->
            <div class="relative shrink-0">
              <img
                v-if="store.currentAlumni.photo"
                :src="store.currentAlumni.photo"
                :alt="store.currentAlumni.name"
                class="h-16 w-16 rounded-full border-4 border-white object-cover shadow"
              />
              <div
                v-else
                class="flex h-16 w-16 items-center justify-center rounded-full border-4 border-white bg-primary-100 shadow text-xl font-bold text-primary-700"
              >
                {{ initials(store.currentAlumni.name) }}
              </div>
            </div>
            <div class="mb-1">
              <h2 class="text-lg font-bold text-gray-900">{{ store.currentAlumni.name }}</h2>
              <p class="text-sm text-gray-500">{{ store.currentAlumni.nim }} · {{ store.currentAlumni.study_program?.name }}</p>
            </div>
          </div>

          <!-- Info Akademik (Read Only) -->
          <div class="mt-4 grid grid-cols-2 gap-3 rounded-lg bg-gray-50 p-4 text-sm sm:grid-cols-4">
            <div>
              <p class="text-xs text-gray-400">Tahun Lulus</p>
              <p class="font-medium text-gray-800">{{ store.currentAlumni.graduation_year ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">IPK</p>
              <p class="font-medium text-gray-800">{{ store.currentAlumni.gpa ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">Fakultas</p>
              <p class="font-medium text-gray-800">{{ store.currentAlumni.study_program?.faculty?.name ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">Email</p>
              <p class="font-medium text-gray-800 truncate">{{ store.currentAlumni.user?.email ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Edit Kontak -->
      <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-semibold text-gray-900">Informasi Kontak</h3>
          <p class="text-xs text-gray-400">* Informasi akademik hanya dapat diubah oleh admin</p>
        </div>

        <form class="space-y-4" @submit.prevent="saveContact">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                placeholder="08xxxxxxxxxx"
              />
              <p v-if="errors.phone" class="mt-1 text-xs text-red-500">{{ errors.phone[0] }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
              <input
                v-model="form.city"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
              <input
                v-model="form.province"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
              <input
                v-model="form.postal_code"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
              <textarea
                v-model="form.address"
                rows="2"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50" @click="resetForm">Reset</button>
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-60"
            >
              <svg v-if="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4Z" />
              </svg>
              {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>

          <p v-if="saveSuccess" class="text-sm text-green-600 font-medium">✓ Profil berhasil diperbarui.</p>
        </form>
      </div>

      <!-- Status Pekerjaan -->
      <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Status Pekerjaan</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Saat Ini</label>
            <select
              v-model="empForm.employment_status"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
              <option value="employed">Bekerja</option>
              <option value="self_employed">Wirausaha</option>
              <option value="unemployed">Belum Bekerja</option>
              <option value="continuing_study">Lanjut Studi</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sedang Bekerja?</label>
            <select
              v-model="empForm.is_employed"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
              <option :value="true">Ya</option>
              <option :value="false">Tidak</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Masa Tunggu (bulan)</label>
            <input
              v-model.number="empForm.waiting_period_months"
              type="number"
              min="0"
              max="120"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            />
          </div>
        </div>
        <div class="mt-4 flex justify-end">
          <button
            :disabled="empSaving"
            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-60"
            @click="saveEmploymentStatus"
          >
            {{ empSaving ? 'Menyimpan...' : 'Perbarui Status' }}
          </button>
        </div>
        <p v-if="empSuccess" class="mt-2 text-sm text-green-600 font-medium">✓ Status pekerjaan diperbarui.</p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { useAlumniStore } from '@/stores/useAlumniStore'

const store = useAlumniStore()

const form = reactive({ phone: '', city: '', province: '', postal_code: '', address: '' })
const empForm = reactive({ employment_status: 'unemployed', is_employed: false, waiting_period_months: 0 })
const errors  = ref({})
const saving  = ref(false)
const saveSuccess = ref(false)
const empSaving   = ref(false)
const empSuccess  = ref(false)

onMounted(async () => {
  await store.fetchMyProfile()
  populateForm()
})

watch(() => store.currentAlumni, populateForm)

function populateForm() {
  const a = store.currentAlumni
  if (!a) return
  form.phone        = a.phone        ?? ''
  form.city         = a.city         ?? ''
  form.province     = a.province     ?? ''
  form.postal_code  = a.postal_code  ?? ''
  form.address      = a.address      ?? ''
  empForm.employment_status    = a.employment_status    ?? 'unemployed'
  empForm.is_employed          = a.is_employed          ?? false
  empForm.waiting_period_months = a.waiting_period_months ?? 0
}

function resetForm() {
  populateForm()
  errors.value    = {}
  saveSuccess.value = false
}

async function saveContact() {
  saving.value      = true
  errors.value      = {}
  saveSuccess.value = false
  try {
    await store.updateMyProfile({ ...form })
    saveSuccess.value = true
    setTimeout(() => (saveSuccess.value = false), 3000)
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors ?? {}
  } finally {
    saving.value = false
  }
}

async function saveEmploymentStatus() {
  empSaving.value = true
  empSuccess.value = false
  try {
    await store.updateMyEmploymentStatus({ ...empForm })
    empSuccess.value = true
    setTimeout(() => (empSuccess.value = false), 3000)
  } catch {}
  finally { empSaving.value = false }
}

function initials(name) {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}
</script>
