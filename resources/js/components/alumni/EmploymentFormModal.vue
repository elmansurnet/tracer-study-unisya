<template>
  <AppModal
    :show="show"
    :title="isEdit ? 'Edit Riwayat Pekerjaan' : 'Tambah Riwayat Pekerjaan'"
    size="lg"
    @close="$emit('close')"
  >
    <form class="space-y-4" @submit.prevent="submit">
      <!-- Error alert -->
      <AppAlert v-if="globalError" variant="error" :show="!!globalError" dismissible @close="globalError = ''">
        {{ globalError }}
      </AppAlert>

      <!-- Row: Institusi (autocomplete) -->
      <AppAutocomplete
        v-model="selectedInstitution"
        label="Institusi / Perusahaan"
        placeholder="Ketik nama perusahaan..."
        :search-fn="searchInstitutionsFn"
        label-key="name"
        value-key="id"
        :error="fieldError('institution_id')"
        required
      >
        <template #item="{ item }">
          <div class="flex flex-col">
            <span class="font-medium">{{ item.name }}</span>
            <span v-if="item.city" class="text-xs text-slate-400">{{ item.city }}</span>
          </div>
        </template>
      </AppAutocomplete>

      <!-- Row: Profesi (autocomplete) -->
      <AppAutocomplete
        v-model="selectedProfession"
        label="Profesi / Bidang Pekerjaan"
        placeholder="Ketik nama profesi..."
        :search-fn="searchProfessionsFn"
        label-key="name"
        value-key="id"
        :error="fieldError('profession_id')"
        required
      >
        <template #item="{ item }">
          <div class="flex flex-col">
            <span class="font-medium">{{ item.name }}</span>
            <span v-if="item.category?.name" class="text-xs text-slate-400">{{ item.category.name }}</span>
          </div>
        </template>
      </AppAutocomplete>

      <!-- Jabatan -->
      <AppInput
        v-model="form.position"
        label="Jabatan / Posisi"
        placeholder="Contoh: Staff Keuangan, Backend Developer"
        :error="fieldError('position')"
        required
      />

      <!-- Jenis pekerjaan + gaji -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppSelect
          v-model="form.employment_type"
          label="Jenis Pekerjaan"
          :options="employmentTypeOptions"
          :error="fieldError('employment_type')"
        />
        <AppInput
          v-model="form.salary"
          label="Gaji (Rp)"
          type="number"
          placeholder="0"
          :min="0"
          hint="Opsional"
          :error="fieldError('salary')"
        />
      </div>

      <!-- Tanggal mulai + selesai -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <AppInput
          v-model="form.start_date"
          label="Tanggal Mulai"
          type="date"
          :error="fieldError('start_date')"
          required
        />
        <div>
          <AppInput
            v-model="form.end_date"
            label="Tanggal Selesai"
            type="date"
            :disabled="form.is_current"
            :error="fieldError('end_date')"
            :hint="form.is_current ? 'Dikosongkan karena masih aktif' : ''"
          />
        </div>
      </div>

      <!-- Toggle is_current -->
      <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
        <button
          type="button"
          role="switch"
          :aria-checked="form.is_current"
          class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
          :class="form.is_current ? 'bg-primary-600' : 'bg-slate-300 dark:bg-slate-600'"
          @click="toggleCurrent"
        >
          <span
            class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition duration-200"
            :class="form.is_current ? 'translate-x-4' : 'translate-x-0'"
          />
        </button>
        <label class="cursor-pointer select-none text-sm text-slate-700 dark:text-slate-300" @click="toggleCurrent">
          Pekerjaan saat ini (masih aktif)
        </label>
      </div>

      <!-- Deskripsi -->
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi <span class="text-slate-400">(opsional)</span></label>
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Deskripsi singkat tugas / tanggung jawab..."
          class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900
                 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20
                 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500"
          :class="{ 'border-red-400': fieldError('description') }"
        />
        <p v-if="fieldError('description')" class="mt-1 text-xs text-red-500">{{ fieldError('description') }}</p>
      </div>
    </form>

    <!-- Footer slot (tombol aksi) -->
    <template #footer>
      <div class="flex items-center justify-end gap-2">
        <AppButton variant="ghost" size="sm" :disabled="saving" @click="$emit('close')">
          Batal
        </AppButton>
        <AppButton variant="primary" size="sm" :loading="saving" @click="submit">
          {{ isEdit ? 'Simpan Perubahan' : 'Tambah' }}
        </AppButton>
      </div>
    </template>
  </AppModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import AppModal        from '@/components/base/AppModal.vue'
import AppInput        from '@/components/base/AppInput.vue'
import AppSelect       from '@/components/base/AppSelect.vue'
import AppButton       from '@/components/base/AppButton.vue'
import AppAlert        from '@/components/base/AppAlert.vue'
import AppAutocomplete from '@/components/base/AppAutocomplete.vue'
import { useAlumniStore } from '@/stores/useAlumniStore'

const props = defineProps({
  show:       { type: Boolean, required: true },
  /** ID alumni (admin context); null untuk alumni self context */
  alumniId:   { type: String, default: null },
  /** Objek history untuk mode edit; null untuk create */
  history:    { type: Object, default: null },
  /** 'admin' | 'self' — menentukan endpoint yang dipakai */
  context:    { type: String, default: 'admin', validator: v => ['admin','self'].includes(v) },
})
const emit = defineEmits(['close', 'saved'])

const store = useAlumniStore()

// ── Autocomplete search functions ───────────────────────────────────────────
const searchInstitutionsFn = (q) =>
  props.context === 'admin'
    ? store.searchInstitutions(q)
    : store.searchInstitutionsPublic(q)

const searchProfessionsFn = (q) =>
  props.context === 'admin'
    ? store.searchProfessions(q)
    : store.searchProfessionsPublic(q)

// ── Options ────────────────────────────────────────────────────────────────
const employmentTypeOptions = [
  { value: '',            label: 'Pilih jenis pekerjaan' },
  { value: 'full_time',   label: 'Full Time' },
  { value: 'part_time',   label: 'Part Time' },
  { value: 'freelance',   label: 'Freelance' },
  { value: 'internship',  label: 'Magang / Internship' },
  { value: 'contract',    label: 'Kontrak' },
  { value: 'entrepreneur',label: 'Wirausaha' },
  { value: 'other',       label: 'Lainnya' },
]

// ── Selected autocomplete state ─────────────────────────────────────────────
const selectedInstitution = ref(null)
const selectedProfession  = ref(null)

// ── Form state ─────────────────────────────────────────────────────────────
const defaultForm = () => ({
  institution_id:  null,
  profession_id:   null,
  position:        '',
  employment_type: '',
  salary:          '',
  start_date:      '',
  end_date:        '',
  is_current:      false,
  description:     '',
})

const form        = ref(defaultForm())
const saving      = ref(false)
const globalError = ref('')
const fieldErrors = ref({})

const isEdit = computed(() => !!props.history)

// Sync autocomplete → form IDs
watch(selectedInstitution, val => { form.value.institution_id = val?.id ?? null })
watch(selectedProfession,  val => { form.value.profession_id  = val?.id ?? null })

// Populate on edit mode
watch(() => props.history, (val) => {
  if (val) {
    form.value = {
      institution_id:  val.institution_id ?? null,
      profession_id:   val.profession_id  ?? null,
      position:        val.position        ?? '',
      employment_type: val.employment_type ?? '',
      salary:          val.salary          ?? '',
      start_date:      val.start_date      ?? '',
      end_date:        val.end_date        ?? '',
      is_current:      val.is_current      ?? false,
      description:     val.description     ?? '',
    }
    selectedInstitution.value = val.institution ?? null
    selectedProfession.value  = val.profession  ?? null
  } else {
    form.value            = defaultForm()
    selectedInstitution.value = null
    selectedProfession.value  = null
  }
  fieldErrors.value = {}
  globalError.value = ''
}, { immediate: true })

// Reset on close
watch(() => props.show, (val) => {
  if (!val) {
    form.value            = defaultForm()
    selectedInstitution.value = null
    selectedProfession.value  = null
    fieldErrors.value = {}
    globalError.value = ''
  }
})

// ── Helpers ─────────────────────────────────────────────────────────────────
function fieldError(field) {
  return fieldErrors.value[field]?.[0] ?? ''
}

function toggleCurrent() {
  form.value.is_current = !form.value.is_current
  if (form.value.is_current) form.value.end_date = ''
}

function validateLocal() {
  const errs = {}
  if (!form.value.institution_id) errs.institution_id = ['Wajib dipilih']
  if (!form.value.profession_id)  errs.profession_id  = ['Wajib dipilih']
  if (!form.value.position?.trim()) errs.position = ['Wajib diisi']
  if (!form.value.start_date)    errs.start_date = ['Wajib diisi']
  fieldErrors.value = errs
  return Object.keys(errs).length === 0
}

// ── Submit ─────────────────────────────────────────────────────────────────
async function submit() {
  if (!validateLocal()) return

  saving.value      = true
  globalError.value = ''
  fieldErrors.value = {}

  try {
    const payload = { ...form.value }
    if (payload.is_current) delete payload.end_date
    if (!payload.salary)    delete payload.salary
    if (!payload.description?.trim()) delete payload.description

    let result
    if (props.context === 'admin') {
      result = isEdit.value
        ? await store.updateEmploymentHistory(props.alumniId, props.history.id, payload)
        : await store.createEmploymentHistory(props.alumniId, payload)
    } else {
      result = isEdit.value
        ? await store.updateMyEmploymentHistory(props.history.id, payload)
        : await store.createMyEmploymentHistory(payload)
    }

    emit('saved', result.data)
  } catch (err) {
    if (err?.response?.status === 422) {
      fieldErrors.value = err.response.data?.errors ?? {}
    } else {
      globalError.value = err?.response?.data?.message ?? 'Terjadi kesalahan. Coba lagi.'
    }
  } finally {
    saving.value = false
  }
}
</script>
