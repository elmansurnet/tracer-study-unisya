<template>
  <AppModal :model-value="modelValue" :title="title" @update:modelValue="$emit('update:modelValue', $event)">
    <div class="space-y-4">
      <div class="flex items-start gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300">
          !
        </div>

        <div class="space-y-1">
          <p class="text-sm text-slate-700 dark:text-slate-300">
            {{ message }}
          </p>
          <p v-if="description" class="text-xs text-slate-500 dark:text-slate-400">
            {{ description }}
          </p>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <AppButton variant="ghost" @click="$emit('update:modelValue', false)">
          Batal
        </AppButton>
        <AppButton variant="danger" :loading="loading" @click="$emit('confirm')">
          Ya, lanjutkan
        </AppButton>
      </div>
    </template>
  </AppModal>
</template>

<script setup>
import AppButton from './AppButton.vue'
import AppModal from './AppModal.vue'

defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Konfirmasi Aksi',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan aksi ini?',
  },
  description: {
    type: String,
    default: '',
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue', 'confirm'])
</script>