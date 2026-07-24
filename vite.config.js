import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  test: {
    environment: 'jsdom',
    globals: true,
    setupFiles: ['./src/renderer/test/setup.ts'],
    exclude: ['node_modules/**', '.worktrees/**'],
  },
  build: {
    outDir: '.',
    emptyOutDir: false,
    rollupOptions: {
      input: {
        'submission-form':     'src/submission/main.jsx',
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
