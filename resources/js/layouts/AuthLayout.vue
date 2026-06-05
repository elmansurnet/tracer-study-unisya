<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="mx-auto flex min-h-screen max-w-7xl items-center justify-center px-4 py-10 sm:px-6">
      <div class="grid w-full max-w-5xl gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="hidden rounded-[32px] bg-primary-700 p-10 text-white shadow-2xl lg:flex lg:flex-col lg:justify-between dark:bg-primary-600 dark:text-slate-950">
          <div class="space-y-6">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-xl font-bold ring-1 ring-white/20 dark:bg-slate-950/10 dark:ring-slate-950/10">
              U
            </div>
            <div class="space-y-3">
              <p class="text-sm font-medium uppercase tracking-[0.25em] text-white/80 dark:text-slate-950/70">
                Universitas Islam Syarifuddin
              </p>
              <h1 class="font-display text-4xl font-bold leading-tight">
                Sistem Informasi Tracer Study UNISYA
              </h1>
              <p class="max-w-xl text-sm leading-7 text-white/85 dark:text-slate-950/80">
                Platform terintegrasi untuk pelacakan alumni, pengelolaan tracer study, serta evaluasi keterkaitan lulusan dengan dunia kerja.
              </p>
            </div>
          </div>

          <div class="rounded-3xl bg-white/10 p-6 ring-1 ring-white/15 backdrop-blur dark:bg-slate-950/10 dark:ring-slate-950/10">
            <p class="text-sm font-semibold">Akses aman dan terstruktur</p>
            <p class="mt-2 text-sm leading-7 text-white/85 dark:text-slate-950/80">
              Login admin, alumni, dan employer dikelola dalam satu sistem dengan autentikasi berlapis dan navigasi berbasis peran.
            </p>
          </div>
        </section>

        <section class="flex items-center">
          <div class="w-full rounded-[28px] border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6 lg:hidden">
              <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600 text-lg font-bold text-white dark:bg-primary-500 dark:text-slate-950">
                U
              </div>
              <h1 class="mt-4 font-display text-2xl font-bold text-slate-900 dark:text-white">
                Tracer Study UNISYA
              </h1>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Silakan masuk untuk melanjutkan akses ke sistem.
              </p>
            </div>

            <slot />
          </div>
        </section>
      </div>
    </div>
  </div>
</template>