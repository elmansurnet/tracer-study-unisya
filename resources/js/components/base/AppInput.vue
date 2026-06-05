<template>
  <div class="space-y-1.5">
    <label
      v-if="label"
      :for="inputId"
      class="block text-sm font-medium text-slate-700 dark:text-slate-200"
    >
      {{ label }}
      <span v-if="required" class="text-red-600 dark:text-red-400">*</span>
    </label>

    <div
      :class="[
        'flex items-center rounded-xl border bg-white transition focus-within:ring-2 focus-within:ring-primary-500/20 dark:bg-slate-900',
        hasError
          ? 'border-red-500 dark:border-red-400'
          : 'border-slate-300 dark:border-slate-700',
      ]"
    >
      <span
        v-if="$slots.prefix"
        class="flex items-center pl-3 text-slate-400 dark:text-slate-500"
      >
        <slot name="prefix" />
      </span>

      <input
        :id="inputId"
        :type="type"
        :model-value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-xl border-0 bg-transparent px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100 dark:placeholder:text-slate-500',
          $slots.prefix ? 'pl-2' : '',
          $slots.suffix ? 'pr-2' : '',
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
      />

      <span
        v-if="$slots.suffix"
        class="flex items-center pr-3 text-slate-400 dark:text-slate-500"
      >
        <slot name="suffix" />
      </span>
    </div>

    <p
      v-if="hint && !hasError"
      class="text-xs text-slate-500 dark:text-slate-400"
    >
      {{ hint }}
    </p>

    <p
      v-if="hasError"
      class="text-xs font-medium text-red-600 dark:text-red-400"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

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
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  autocomplete: {
    type: String,
    default: 'off',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue', 'blur'])

const inputId = computed(() => props.id || `input-${Math.random().toString(36).slice(2, 9)}`)
const hasError = computed(() => Boolean(props.error))
</script>