import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  test: {
    environment: 'jsdom',
    globals: true,
    setupFiles: ['./src/renderer/test/setup.ts'],
  },
  build: {
    rollupOptions: {
      input: {
        builder:               'src/builder/main.tsx',
        'reflection-renderer': 'src/renderer/main.tsx',
      },
      output: {
        entryFileNames: 'assets/js/[name].js',
        chunkFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name][extname]',
      },
    },
  },
});
// Outputs: assets/js/builder.js + assets/js/reflection-renderer.js
