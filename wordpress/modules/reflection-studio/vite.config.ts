import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    rollupOptions: {
      input: {
        builder:  resolve(__dirname, 'src/builder/main.tsx'),
        renderer: resolve(__dirname, 'src/renderer/main.tsx'),
      },
      output: {
        entryFileNames: 'assets/js/studio-[name].js',
        chunkFileNames:  'assets/js/[name]-[hash].js',
        assetFileNames:  'assets/[ext]/[name].[ext]',
      },
    },
    outDir: '.',
    emptyOutDir: false,
  },
  resolve: { alias: { '@': resolve(__dirname, 'src') } },
  test: {
    environment: 'jsdom',
    setupFiles:  ['src/test/setup.ts'],
    globals: true,
  },
});
