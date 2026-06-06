<script setup>
import { onMounted, reactive, watch } from 'vue'
import { useInstitutionDetailStore } from '@/stores/useInstitutionDetailStore'

const props = defineProps({
  institutionId: {
    type:     String,
    required: true,
  },
})

const store = useInstitutionDetailStore()
const form  = reactive({
  website:      '',
  phone:        '',
  email:        '',
  address:      '',
  city:         '',
  province:     '',
  postal_code:  '',
  description:  '',
  logo_url:     '',
  linkedin_url: '',
})

onMounted(async () => {
  await store.fetchByInstitution(props.institutionId)
  fillForm()
})

watch(() => store.detail, fillForm, { deep: true })

function fillForm() {
  if (!store.detail) return
  Object.keys(form).forEach(k => {
    form[k] = store.detail[k] ?? ''
  })
}

async function save() {
  try {
    await store.save(props.institutionId, { ...form })
  } catch {}
}
</script>

<template>
  <div class="space-y-5">
    <!-- Success -->
    <div
      v-if="store.successMsg"
      class="rounded-lg bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300"
    >
      {{ store.successMsg }}
    </div>
    <!-- Error -->
    <div
      v-if="store.error && typeof store.error === 'string'"
      class="rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400"
    >
      {{ store.error }}
    </div>

    <div v-if="store.isLoading" class="flex justify-center py-10">
      <svg class="h-7 w-7 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
      </svg>
    </div>

    <form v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="save">
      <div>
        <label class="form-label">Website</label>
        <input v-model="form.website" type="url" class="input-base w-full" placeholder="https://..." />
        <p v-if="store.error?.website" class="form-error">{{ store.error.website[0] }}</p>
      </div>
      <div>
        <label class="form-label">Telepon</label>
        <input v-model="form.phone" type="text" class="input-base w-full" placeholder="021-XXXXXX" />
      </div>
      <div>
        <label class="form-label">Email</label>
        <input v-model="form.email" type="email" class="input-base w-full" placeholder="info@perusahaan.com" />
        <p v-if="store.error?.email" class="form-error">{{ store.error.email[0] }}</p>
      </div>
      <div>
        <label class="form-label">Kode Pos</label>
        <input v-model="form.postal_code" type="text" class="input-base w-full" maxlength="10" />
      </div>
      <div class="sm:col-span-2">
        <label class="form-label">Alamat</label>
        <textarea v-model="form.address" rows="2" class="input-base w-full" />
      </div>
      <div>
        <label class="form-label">Kota</label>
        <input v-model="form.city" type="text" class="input-base w-full" />
      </div>
      <div>
        <label class="form-label">Provinsi</label>
        <input v-model="form.province" type="text" class="input-base w-full" />
      </div>
      <div>
        <label class="form-label">URL Logo</label>
        <input v-model="form.logo_url" type="url" class="input-base w-full" placeholder="https://..." />
        <p v-if="store.error?.logo_url" class="form-error">{{ store.error.logo_url[0] }}</p>
      </div>
      <div>
        <label class="form-label">URL LinkedIn</label>
        <input v-model="form.linkedin_url" type="url" class="input-base w-full" placeholder="https://linkedin.com/company/..." />
        <p v-if="store.error?.linkedin_url" class="form-error">{{ store.error.linkedin_url[0] }}</p>
      </div>
      <div class="sm:col-span-2">
        <label class="form-label">Deskripsi</label>
        <textarea v-model="form.description" rows="3" class="input-base w-full" maxlength="2000" />
      </div>

      <div class="sm:col-span-2 flex justify-end">
        <button type="submit" class="btn-primary" :disabled="store.isSaving">
          {{ store.isSaving ? 'Menyimpan...' : 'Simpan Detail' }}
        </button>
      </div>
    </form>
  </div>
</template>
