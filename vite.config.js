import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
    },
  },
  build: {
    // Target modern browsers yang mendukung ES modules natively.
    // 'esnext' memungkinkan top-level await, dynamic import, dan semua
    // fitur ES modern tanpa transpilasi berlebihan.
    // Browser lama yang tidak masuk daftar ini tidak didukung (sesuai arsitektur SPA).
    target: 'esnext',
    // Chunk warning threshold — default 500KB terlalu ketat untuk SPA penuh
    chunkSizeWarningLimit: 1000,
    rollupOptions: {
      output: {
        // Manual chunk splitting untuk performa loading:
        // vendor (Vue core) dimuat terpisah agar bisa di-cache browser secara independen
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          http: ['axios'],
        },
      },
    },
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    hmr: {
      host: '127.0.0.1',
    },
  },
})
