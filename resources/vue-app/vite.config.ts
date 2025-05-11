import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  root: 'resources/vue-app',        // where your main.ts lives
  base: '/build/',                  // public URL prefix for built files
  plugins: [ vue() ],
  resolve: {
    alias: { '@': path.resolve(__dirname, 'resources/vue-app') }
  },
  build: {
    manifest: true,                 // emit manifest.json
    outDir: '../public/build',      // write into Laravel’s public/build
    emptyOutDir: true,              // clear old builds
    rollupOptions: {
      // point this at your actual entry file:
      input: path.resolve(__dirname, 'resources/vue-app/main.ts'),
    },
  },
});
