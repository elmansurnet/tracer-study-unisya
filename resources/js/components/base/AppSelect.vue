<template>
  <div class="space-y-1.5">
    <label
      v-if="label"
      :for="selectId"
      class="block text-sm font-medium text-slate-700 dark:text-slate-200"
    >
      {{ label }}
      <span v-if="required" class="text-red-600 dark:text-red-400">*</span>
    </label>

    <div class="space-y-2">
      <div v-if="searchable" class="relative">
        <input
          v-model="search"
          type="text"
          class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          placeholder="Cari opsi..."
        />
      </div>

      <select
        :id="selectId"
        :value="modelValue"
        :disabled="disabled"
        :class="[
          'block w-full rounded-xl border bg-white px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:bg-slate-900',
          hasError
            ? 'border-red-500 text-red-700 dark:border-red-400 dark:text-red-300'
            : 'border-slate-300 text-slate-900 dark:border-slate-700 dark:text-slate-100',
        ]"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option value="">{{ placeholder }}</option>
        <option
          v-for="option in filteredOptions"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>

    <p v-if="error" class="text-xs font-medium text-red-600 dark:text-red-400">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Pilih salah satu',
  },
  error: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    default: () => [],
  },
  searchable: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])

const search = ref('')
const selectId = computed(() => props.id || `select-${Math.random().toString(36).slice(2, 9)}`)
const hasError = computed(() => Boolean(props.error))

const filteredOptions = computed(() => {
  if (!props.searchable || !search.value.trim()) {
    return props.options
  }

  const keyword = search.value.toLowerCase()

  return props.options.filter((option) =>
    String(option.label).toLowerCase().includes(keyword),
  )
})
</script>