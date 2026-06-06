<template>
  <div class="space-y-6">
    <!-- Step indicator -->
    <div class="flex items-center gap-0">
      <template v-for="(step, idx) in steps" :key="step.key">
        <div class="flex flex-col items-center">
          <div
            class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold transition"
            :class="stepCircleClass(idx)"
          >
            <svg v-if="idx < currentStep" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span v-else>{{ idx + 1 }}</span>
          </div>
          <span class="mt-1 text-xs" :class="idx <= currentStep ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-slate-400'">
            {{ step.label }}
          </span>
        </div>
        <div
          v-if="idx < steps.length - 1"
          class="mb-4 h-0.5 flex-1 transition"
          :class="idx < currentStep ? 'bg-primary-500' : 'bg-slate-200 dark:bg-slate-700'"
        />
      </template>
    </div>

    <!-- Error alert global -->
    <AppAlert v-if="globalError" variant="error" :show="!!globalError" dismissible @close="globalError = ''">
      {{ globalError }}
    </AppAlert>

    <!-- ── Step 1: Data Pribadi ──────────────────────────────────────────── -->
    <div v-show="currentStep === 0" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.full_name"
          label="Nama Lengkap"
          placeholder="Masukkan nama lengkap"
          :error="fieldError('full_name')"
          required
        />
        <AppInput
          v-model="form.nik"
          label="NIK"
          placeholder="16 digit NIK"
          maxlength="16"
          :error="fieldError('nik')"
        />
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppSelect
          v-model="form.gender"
          label="Jenis Kelamin"
          :options="genderOptions"
          :error="fieldError('gender')"
          required
        />
        <AppInput
          v-model="form.birth_date"
          label="Tanggal Lahir"
          type="date"
          :error="fieldError('birth_date')"
        />
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.phone"
          label="No. Telepon / HP"
          placeholder="08xx-xxxx-xxxx"
          :error="fieldError('phone')"
        />
        <AppInput
          v-model="form.email"
          label="Email"
          type="email"
          placeholder="email@domain.com"
          :error="fieldError('email')"
        />
      </div>
      <AppInput
        v-model="form.address"
        label="Alamat"
        placeholder="Alamat lengkap"
        :error="fieldError('address')"
      />
    </div>

    <!-- ── Step 2: Data Akademik ─────────────────────────────────────────── -->
    <div v-show="currentStep === 1" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.nim"
          label="NIM"
          placeholder="Nomor Induk Mahasiswa"
          :error="fieldError('nim')"
          required
        />
        <AppSelect
          v-model="form.study_program_id"
          label="Program Studi"
          :options="studyProgramOptions"
          :error="fieldError('study_program_id')"
          required
        />
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.graduation_year"
          label="Tahun Lulus / Angkatan"
          type="number"
          placeholder="2024"
          :min="2000"
          :max="currentYear"
          :error="fieldError('graduation_year')"
          required
        />
        <AppSelect
          v-model="form.class_entry_year"
          label="Tahun Masuk"
          :options="entryYearOptions"
          :error="fieldError('class_entry_year')"
        />
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.entry_date"
          label="Tanggal Masuk"
          type="date"
          :error="fieldError('entry_date')"
        />
        <AppInput
          v-model="form.graduation_date"
          label="Tanggal Lulus"
          type="date"
          :error="fieldError('graduation_date')"
        />
      </div>
      <AppInput
        v-model="form.thesis_title"
        label="Judul Skripsi / Tugas Akhir"
        placeholder="Opsional"
        :error="fieldError('thesis_title')"
      />
    </div>

    <!-- ── Step 3: Status Pekerjaan ─────────────────────────────────────── -->
    <div v-show="currentStep === 2" class="space-y-4">
      <!-- Toggle is_employed -->
      <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
        <div>
          <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Sudah Bekerja?</p>
          <p class="text-xs text-slate-500 dark:text-slate-400">Centang jika alumni sudah memiliki pekerjaan saat ini</p>
        </div>
        <button
          type="button"
          role="switch"
          :aria-checked="form.is_employed"
          class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          :class="form.is_employed ? 'bg-primary-600' : 'bg-slate-300 dark:bg-slate-600'"
          @click="form.is_employed = !form.is_employed"
        >
          <span
            class="pointer-events-none inline-block h-5 w-5 translate-x-0 rounded-full bg-white shadow ring-0 transition duration-200"
            :class="form.is_employed ? 'translate-x-5' : 'translate-x-0'"
          />
        </button>
      </div>

      <!-- Masa tunggu (jika belum bekerja) -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div v-if="!form.is_employed" class="space-y-3">
          <AppInput
            v-model="form.waiting_period_months"
            label="Masa Tunggu (bulan)"
            type="number"
            placeholder="0"
            :min="0"
            :max="120"
            hint="Jumlah bulan sejak lulus hingga mendapat pekerjaan pertama (0 = belum pernah bekerja)"
            :error="fieldError('waiting_period_months')"
          />
        </div>
      </Transition>

      <!-- Info summary -->
      <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4 text-xs text-blue-700 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-300">
        <p class="font-semibold">ℹ️ Catatan</p>
        <p class="mt-1">Detail riwayat pekerjaan dapat ditambahkan setelah data alumni tersimpan melalui tab Riwayat Pekerjaan.</p>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
      <AppButton
        v-if="currentStep > 0"
        variant="ghost"
        size="sm"
        :disabled="saving"
        @click="prev"
      >
        ← Sebelumnya
      </AppButton>
      <span v-else />

      <div class="flex gap-2">
        <AppButton variant="ghost" size="sm" :disabled="saving" @click="$emit('cancel')">
          Batal
        </AppButton>
        <AppButton
          v-if="currentStep < steps.length - 1"
          variant="primary"
          size="sm"
          @click="next"
        >
          Selanjutnya →
        </AppButton>
        <AppButton
          v-else
          variant="primary"
          size="sm"
          :loading="saving"
          @click="submit"
        >
          {{ isEdit ? 'Simpan Perubahan' : 'Simpan Alumni' }}
        </AppButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import AppInput    from '@/components/base/AppInput.vue'
import AppSelect   from '@/components/base/AppSelect.vue'
import AppButton   from '@/components/base/AppButton.vue'
import AppAlert    from '@/components/base/AppAlert.vue'
import { useAlumniStore }  from '@/stores/useAlumniStore'
import { useProgramStore } from '@/stores/useProgramStore'

const props = defineProps({
  /** Objek alumni untuk mode edit; null untuk mode create */
  alumni: { type: Object, default: null },
})
const emit = defineEmits(['saved', 'cancel'])

const alumniStore  = useAlumniStore()
const programStore = useProgramStore()

// ── Options ────────────────────────────────────────────────────────────────
const currentYear = new Date().getFullYear()

const genderOptions = [
  { value: '', label: 'Pilih jenis kelamin' },
  { value: 'L', label: 'Laki-laki' },
  { value: 'P', label: 'Perempuan' },
]

const studyProgramOptions = computed(() => [
  { value: '', label: 'Pilih program studi' },
  ...(programStore.programs ?? []).map(p => ({ value: p.id, label: p.name })),
])

const entryYearOptions = computed(() => {
  const opts = [{ value: '', label: 'Pilih tahun masuk' }]
  for (let y = currentYear; y >= 2000; y--) {
    opts.push({ value: y, label: String(y) })
  }
  return opts
})

// ── Steps ──────────────────────────────────────────────────────────────────
const steps = [
  { key: 'pribadi',  label: 'Data Pribadi' },
  { key: 'akademik', label: 'Akademik' },
  { key: 'kerja',    label: 'Pekerjaan' },
]
const currentStep = ref(0)

const stepCircleClass = (idx) => {
  if (idx < currentStep.value)  return 'bg-primary-600 text-white'
  if (idx === currentStep.value) return 'bg-primary-600 text-white ring-2 ring-primary-300'
  return 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400'
}

// ── Form state ─────────────────────────────────────────────────────────────
const defaultForm = () => ({
  full_name:            '',
  nik:                  '',
  gender:               '',
  birth_date:           '',
  phone:                '',
  email:                '',
  address:              '',
  nim:                  '',
  study_program_id:     '',
  graduation_year:      '',
  class_entry_year:     '',
  entry_date:           '',
  graduation_date:      '',
  thesis_title:         '',
  is_employed:          false,
  waiting_period_months: 0,
})

const form        = ref(defaultForm())
const saving      = ref(false)
const globalError = ref('')
const fieldErrors = ref({})

const isEdit = computed(() => !!props.alumni)

// Populate form for edit mode
watch(() => props.alumni, (val) => {
  if (val) {
    form.value = {
      full_name:             val.full_name ?? '',
      nik:                   val.nik ?? '',
      gender:                val.gender ?? '',
      birth_date:            val.birth_date ?? '',
      phone:                 val.phone ?? '',
      email:                 val.email ?? '',
      address:               val.address ?? '',
      nim:                   val.nim ?? '',
      study_program_id:      val.study_program_id ?? '',
      graduation_year:       val.graduation_year ?? '',
      class_entry_year:      val.class_entry_year ?? '',
      entry_date:            val.entry_date ?? '',
      graduation_date:       val.graduation_date ?? '',
      thesis_title:          val.thesis_title ?? '',
      is_employed:           val.is_employed ?? false,
      waiting_period_months: val.waiting_period_months ?? 0,
    }
  } else {
    form.value = defaultForm()
  }
  currentStep.value = 0
  fieldErrors.value = {}
  globalError.value = ''
}, { immediate: true })

// ── Validation helpers ─────────────────────────────────────────────────────
const stepFields = {
  0: ['full_name', 'gender', 'phone', 'email'],
  1: ['nim', 'study_program_id', 'graduation_year'],
  2: [],
}

function fieldError(field) {
  return fieldErrors.value[field]?.[0] ?? ''
}

function validateStep(step) {
  const required = stepFields[step] ?? []
  const errs = {}
  required.forEach(f => {
    if (!form.value[f] && form.value[f] !== 0) errs[f] = ['Wajib diisi']
  })
  if (step === 0 && form.value.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errs.email = ['Format email tidak valid']
  }
  if (step === 1 && form.value.nim && form.value.nim.toString().length < 3) {
    errs.nim = ['NIM terlalu pendek']
  }
  fieldErrors.value = { ...fieldErrors.value, ...errs }
  return Object.keys(errs).length === 0
}

// ── Navigation ─────────────────────────────────────────────────────────────
function next() {
  if (validateStep(currentStep.value)) currentStep.value++
}
function prev() {
  currentStep.value--
}

// ── Submit ─────────────────────────────────────────────────────────────────
async function submit() {
  if (!validateStep(currentStep.value)) return

  saving.value      = true
  globalError.value = ''
  fieldErrors.value = {}

  try {
    const payload = { ...form.value }
    // Kosongkan field tidak relevan
    if (payload.is_employed) delete payload.waiting_period_months

    let result
    if (isEdit.value) {
      result = await alumniStore.updateAlumni(props.alumni.id, payload)
    } else {
      result = await alumniStore.createAlumni(payload)
    }
    emit('saved', result.data)
  } catch (err) {
    if (err?.response?.status === 422) {
      const errs = err.response.data?.errors ?? {}
      fieldErrors.value = errs
      // Navigasi ke step yang berisi error
      for (let step = 0; step <= 2; step++) {
        const stepF = [
          ['full_name','nik','gender','birth_date','phone','email','address'],
          ['nim','study_program_id','graduation_year','class_entry_year','entry_date','graduation_date','thesis_title'],
          ['is_employed','waiting_period_months'],
        ][step]
        if (stepF.some(f => errs[f])) {
          currentStep.value = step
          break
        }
      }
    } else {
      globalError.value = err?.response?.data?.message ?? 'Terjadi kesalahan. Coba lagi.'
    }
  } finally {
    saving.value = false
  }
}

// Load program studi
if (!programStore.programs?.length) programStore.fetchPrograms()
</script>
