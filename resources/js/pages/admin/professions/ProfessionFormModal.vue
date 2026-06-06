<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { useProfessionStore } from '@/stores/useProfessionStore'
import { useProfessionCategoryStore } from '@/stores/useProfessionCategoryStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({ profession: { type: Object, default: null } })
const emit  = defineEmits(['close', 'saved'])

const store    = useProfessionStore()
const catStore = useProfessionCategoryStore()
const isEdit   = computed(() => !!props.profession)
const form     = ref({ name: '', description: '', profession_category_id: '', is_active: true })
const errors   = ref({})
const loading  = ref(false)

onMounted(() => catStore.fetchAllCategories())

watch(() => props.profession, (p) => {
  form.value = p
    ? {
        name:                    p.name,
        description:             p.description ?? '',
        profession_category_id:  p.profession_category_id,
        is_active:               p.is_active,
      }
    : { name: '', description: '', profession_category_id: '', is_active: true }
}, { immediate: true })

const submit = async () => {
  errors.value = {}; loading.value = true
  try {
    if (isEdit.value) await store.updateProfession(props.profession.id, form.value)
    else              await store.createProfession(form.value)
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors ?? {}
  } finally { loading.value = false }
}
</script>

<template>
  <AppModal :title="isEdit ? 'Edit Profesi' : 'Tambah Profesi'" size="sm" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <!-- Kategori -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Kategori Profesi <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.profession_category_id"
          class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
          required
        >
          <option value="" disabled>Pilih kategori profesi</option>
          <option v-for="cat in catStore.allCategories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <p v-if="errors.profession_category_id?.[0]" class="text-xs text-red-500">
          {{ errors.profession_category_id[0] }}
        </p>
      </div>

      <AppInput
        v-model="form.name"
        label="Nama Profesi"
        placeholder="Contoh: Software Engineer"
        :error="errors.name?.[0]"
        required
      />

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Deskripsi singkat profesi (opsional)"
          class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-primary-500 transition resize-none"
        />
        <p v-if="errors.description?.[0]" class="text-xs text-red-500">{{ errors.description[0] }}</p>
      </div>

      <div class="flex items-center gap-2">
        <input id="prof-active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" />
        <label for="prof-active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">{{ isEdit ? 'Simpan' : 'Tambah' }}</AppButton>
      </div>
    </form>
  </AppModal>
</template>
