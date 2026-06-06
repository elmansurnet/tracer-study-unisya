<script setup>
import { ref, watch, computed } from 'vue'
import { useStudyProgramStore } from '@/stores/useStudyProgramStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppSelect from '@/components/base/AppSelect.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({
  program:   { type: Object, default: null },
  faculties: { type: Array, default: () => [] },
})
const emit  = defineEmits(['close', 'saved'])
const store  = useStudyProgramStore()
const isEdit = computed(() => !!props.program)

const form   = ref({ name: '', code: '', degree: 'S1', faculty_id: '', is_active: true })
const errors = ref({})
const loading = ref(false)

const degreeOptions = [
  { value: 'D3', label: 'D3' },
  { value: 'S1', label: 'S1' },
  { value: 'S2', label: 'S2' },
]

const facultyOptions = computed(() =>
  props.faculties.map(f => ({ value: f.id, label: f.name }))
)

watch(() => props.program, (p) => {
  form.value = p
    ? { name: p.name, code: p.code, degree: p.degree, faculty_id: p.faculty_id, is_active: p.is_active }
    : { name: '', code: '', degree: 'S1', faculty_id: '', is_active: true }
}, { immediate: true })

const submit = async () => {
  errors.value = {}; loading.value = true
  try {
    if (isEdit.value) await store.updateStudyProgram(props.program.id, form.value)
    else              await store.createStudyProgram(form.value)
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors ?? {}
  } finally { loading.value = false }
}
</script>

<template>
  <AppModal :title="isEdit ? 'Edit Program Studi' : 'Tambah Program Studi'" size="md" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <AppSelect v-model="form.faculty_id" label="Fakultas" :options="facultyOptions" :error="errors.faculty_id?.[0]" />
      <AppInput v-model="form.name" label="Nama Program Studi" placeholder="Contoh: Teknik Informatika" :error="errors.name?.[0]" required />
      <div class="grid grid-cols-2 gap-4">
        <AppInput v-model="form.code" label="Kode" placeholder="Contoh: TI" :error="errors.code?.[0]" required />
        <AppSelect v-model="form.degree" label="Jenjang" :options="degreeOptions" :error="errors.degree?.[0]" />
      </div>
      <div class="flex items-center gap-2">
        <input id="sp_active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" />
        <label for="sp_active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">{{ isEdit ? 'Simpan' : 'Tambah' }}</AppButton>
      </div>
    </form>
  </AppModal>
</template>