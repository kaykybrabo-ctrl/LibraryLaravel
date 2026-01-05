import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/admin/src/app.js', 'resources/css/app.css'],
      refresh: true,
    }),
    vue(),
  ],
  server: {
    host: true,
    port: 5173,
    cors: true,
    hmr: {
      host: 'localhost',
      protocol: 'ws',
      port: 5173,
    },
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm.js',
    },
  },
});
