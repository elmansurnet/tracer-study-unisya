<script setup>
import { ref, watch, computed } from 'vue'
import { useUserStore } from '@/stores/useUserStore'
import AppModal from '@/components/base/AppModal.vue'
import AppInput from '@/components/base/AppInput.vue'
import AppSelect from '@/components/base/AppSelect.vue'
import AppButton from '@/components/base/AppButton.vue'

const props = defineProps({
  user: { type: Object, default: null },
})
const emit = defineEmits(['close', 'saved'])

const store  = useUserStore()
const isEdit = computed(() => !!props.user)

const form = ref({
  name:     '',
  email:    '',
  phone:    '',
  role:     'alumni',
  password: '',
  is_active: true,
})
const errors  = ref({})
const loading = ref(false)

const roleOptions = [
  { value: 'super_admin', label: 'Super Admin' },
  { value: 'alumni',      label: 'Alumni' },
]

watch(() => props.user, (u) => {
  if (u) {
    form.value = {
      name:      u.name,
      email:     u.email,
      phone:     u.phone ?? '',
      role:      u.role,
      password:  '',
      is_active: u.is_active,
    }
  } else {
    form.value = { name: '', email: '', phone: '', role: 'alumni', password: '', is_active: true }
  }
}, { immediate: true })

const submit = async () => {
  errors.value  = {}
  loading.value = true
  try {
    if (isEdit.value) {
      await store.updateUser(props.user.id, form.value)
    } else {
      await store.createUser(form.value)
    }
    emit('saved')
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AppModal
    :title="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'"
    size="md"
    @close="emit('close')"
  >
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nama Lengkap"
        placeholder="Masukkan nama lengkap"
        :error="errors.name?.[0]"
        required
      />
      <AppInput
        v-model="form.email"
        label="Email"
        type="email"
        placeholder="contoh@email.com"
        :error="errors.email?.[0]"
        required
      />
      <AppInput
        v-model="form.phone"
        label="Nomor Telepon"
        placeholder="08xxxxxxxxxx"
        :error="errors.phone?.[0]"
      />
      <AppSelect
        v-model="form.role"
        label="Role"
        :options="roleOptions"
        :error="errors.role?.[0]"
      />
      <AppInput
        v-model="form.password"
        label="Password"
        type="password"
        :placeholder="isEdit ? 'Kosongkan jika tidak ingin mengubah' : 'Masukkan password'"
        :error="errors.password?.[0]"
        :required="!isEdit"
      />
      <div class="flex items-center gap-2">
        <input
          id="is_active"
          v-model="form.is_active"
          type="checkbox"
          class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
        />
        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Pengguna Aktif</label>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <AppButton variant="ghost" type="button" @click="emit('close')">Batal</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">
          {{ isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
        </AppButton>
      </div>
    </form>
  </AppModal>
</template>