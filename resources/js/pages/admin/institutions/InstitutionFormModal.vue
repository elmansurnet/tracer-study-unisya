<script setup>
import { ref, watch, computed } from 'vue'
import { useInstitutionStore } from '@/stores/useInstitutionStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({ institution: { type: Object, default: null } })
const emit  = defineEmits(['close', 'saved'])

const store   = useInstitutionStore()
const isEdit  = computed(() => !!props.institution)
const form    = ref({
  name:    '',
  type:    '',
  sector:  '',
  website: '',
  logo:    '',
  is_active: true,
})
const errors  = ref({})
const loading = ref(false)

const institutionTypes = [
  { value: 'perusahaan',  label: 'Perusahaan' },
  { value: 'instansi',    label: 'Instansi Pemerintah' },
  { value: 'pendidikan',  label: 'Lembaga Pendidikan' },
  { value: 'wirausaha',   label: 'Wirausaha' },
  { value: 'ngo',         label: 'NGO / LSM' },
  { value: 'lainnya',     label: 'Lainnya' },
]

watch(() => props.institution, (i) => {
  form.value = i
    ? {
        name:      i.name,
        type:      i.type      ?? '',
        sector:    i.sector    ?? '',
        website:   i.website   ?? '',
        logo:      i.logo      ?? '',
        is_active: i.is_active,
      }
    : { name: '', type: '', sector: '', website: '', logo: '', is_active: true }
}, { immediate: true })

const submit = async () => {
  errors.value = {}; loading.value = true
  try {
    if (isEdit.value) await store.updateInstitution(props.institution.id, form.value)
    else              await store.createInstitution(form.value)
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors ?? {}
  } finally { loading.value = false }
}
</script>

<template>
  <AppModal :title="isEdit ? 'Edit Institusi' : 'Tambah Institusi'" size="md" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nama Institusi"
        placeholder="Contoh: PT. Telkom Indonesia"
        :error="errors.name?.[0]"
        required
      />

      <!-- Tipe -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Institusi</label>
        <select
          v-model="form.type"
          class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        >
          <option value="">Pilih tipe institusi</option>
          <option v-for="t in institutionTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
        </select>
        <p v-if="errors.type?.[0]" class="text-xs text-red-500">{{ errors.type[0] }}</p>
      </div>

      <!-- Grid 2 kolom: Sektor + Website -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <AppInput
          v-model="form.sector"
          label="Sektor"
          placeholder="Contoh: Teknologi"
          :error="errors.sector?.[0]"
        />
        <AppInput
          v-model="form.website"
          label="Website"
          placeholder="https://example.com"
          :error="errors.website?.[0]"
        />
      </div>

      <AppInput
        v-model="form.logo"
        label="URL Logo"
        placeholder="https://example.com/logo.png (opsional)"
        :error="errors.logo?.[0]"
      />

      <div class="flex items-center gap-2">
        <input id="inst-active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" />
        <label for="inst-active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">{{ isEdit ? 'Simpan' : 'Tambah' }}</AppButton>
      </div>
    </form>
  </AppModal>
</template>
