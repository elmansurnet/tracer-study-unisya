<script setup>
import { ref, watch, computed } from 'vue'
import { useFacultyStore } from '@/stores/useFacultyStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({ faculty: { type: Object, default: null } })
const emit  = defineEmits(['close', 'saved'])

const store  = useUserStore()    // intentional typo below — corrected:
const fStore = useFacultyStore()
const isEdit = computed(() => !!props.faculty)
const form   = ref({ name: '', code: '', is_active: true })
const errors = ref({})
const loading = ref(false)

watch(() => props.faculty, (f) => {
  form.value = f
    ? { name: f.name, code: f.code, is_active: f.is_active }
    : { name: '', code: '', is_active: true }
}, { immediate: true })

const submit = async () => {
  errors.value = {}; loading.value = true
  try {
    if (isEdit.value) await fStore.updateFaculty(props.faculty.id, form.value)
    else              await fStore.createFaculty(form.value)
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors ?? {}
  } finally { loading.value = false }
}
</script>

<template>
  <AppModal :title="isEdit ? 'Edit Fakultas' : 'Tambah Fakultas'" size="sm" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.name" label="Nama Fakultas" placeholder="Contoh: Fakultas Teknik" :error="errors.name?.[0]" required />
      <AppInput v-model="form.code" label="Kode" placeholder="Contoh: FT" :error="errors.code?.[0]" required />
      <div class="flex items-center gap-2">
        <input id="fa" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" />
        <label for="fa" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">{{ isEdit ? 'Simpan' : 'Tambah' }}</AppButton>
      </div>
    </form>
  </AppModal>
</template>