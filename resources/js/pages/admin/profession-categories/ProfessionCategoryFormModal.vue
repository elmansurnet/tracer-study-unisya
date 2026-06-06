<script setup>
import { ref, watch, computed } from 'vue'
import { useProfessionCategoryStore } from '@/stores/useProfessionCategoryStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({ category: { type: Object, default: null } })
const emit  = defineEmits(['close', 'saved'])

const store   = useProfessionCategoryStore()
const isEdit  = computed(() => !!props.category)
const form    = ref({ name: '', description: '', is_active: true })
const errors  = ref({})
const loading = ref(false)

watch(() => props.category, (c) => {
  form.value = c
    ? { name: c.name, description: c.description ?? '', is_active: c.is_active }
    : { name: '', description: '', is_active: true }
}, { immediate: true })

const submit = async () => {
  errors.value = {}; loading.value = true
  try {
    if (isEdit.value) await store.updateProfessionCategory(props.category.id, form.value)
    else              await store.createProfessionCategory(form.value)
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors ?? {}
  } finally { loading.value = false }
}
</script>

<template>
  <AppModal :title="isEdit ? 'Edit Kategori Profesi' : 'Tambah Kategori Profesi'" size="sm" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nama Kategori"
        placeholder="Contoh: Teknologi Informasi"
        :error="errors.name?.[0]"
        required
      />
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Deskripsi singkat kategori profesi (opsional)"
          class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-primary-500 transition resize-none"
        />
        <p v-if="errors.description?.[0]" class="text-xs text-red-500">{{ errors.description[0] }}</p>
      </div>
      <div class="flex items-center gap-2">
        <input id="pc-active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" />
        <label for="pc-active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">{{ isEdit ? 'Simpan' : 'Tambah' }}</AppButton>
      </div>
    </form>
  </AppModal>
</template>
