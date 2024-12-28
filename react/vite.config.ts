import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig(({ command }) => {
  if (command === 'serve') {
    // 开发环境配置
    return {
      plugins: [react()],
      server: {
        port: 3000,
        open: true
      }
    }
  } else {
    // 生产环境配置
    return {
      plugins: [react()],
      build: {
        outDir: '../public/static/script/superjson',
        rollupOptions: {
          input: 'src/main.tsx',
          external: ['react', 'react-dom'],
          output: {
            format: 'umd',
            name: 'JSONTools',
            entryFileNames: 'json-tools.umd.js',
            globals: {
              react: 'React',
              'react-dom': 'ReactDOM'
            },
            inlineDynamicImports: true
          }
        }
      }
    }
  }
}) 