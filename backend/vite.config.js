import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
    // Загружаем env
    const env = loadEnv(mode, process.cwd(), '')

    const DEV_HOST = env.VITE_DEV_HOST || 'localhost'
    const DEV_PORT = Number(env.VITE_DEV_PORT || 5173)

    return {
        plugins: [
            laravel({
                input: ['resources/js/app.js'],
                refresh: true,
            }),
            vue(),
            tailwindcss(),
        ],

        server: {
            host: '0.0.0.0',
            port: DEV_PORT,
            strictPort: true,
            cors: true,

            hmr: {
                host: DEV_HOST,
                protocol: 'ws',
                clientPort: DEV_PORT,
            },
        },
    }
})
