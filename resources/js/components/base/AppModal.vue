<template>
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4 py-6"
        @click.self="$emit('update:modelValue', false)"
      >
        <div
          class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
          role="dialog"
          aria-modal="true"
        >
          <div
            v-if="$slots.header || title"
            class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800"
          >
            <div>
              <slot name="header">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                  {{ title }}
                </h2>
              </slot>
            </div>

            <button
              type="button"
              class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
              aria-label="Tutup modal"
              @click="$emit('update:modelValue', false)"
            >
              ✕
            </button>
          </div>

          <div class="px-6 py-5">
            <slot />
          </div>

          <div
            v-if="$slots.footer"
            class="border-t border-slate-200 px-6 py-4 dark:border-slate-800"
          >
            <slot name="footer" />
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>