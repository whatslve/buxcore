import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [

        laravel({
            // CSS тянем импортом в app.js; отдельный input для CSS в dev не нужен
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        // в dev обычно достаточно CORS: true; отказ соединения — не про CORS
        cors: true,
        // важное: HMR указывает реальный IP, по которому к контейнеру коннектится браузер
        hmr: {
            host: '192.168.1.117',
            protocol: 'ws',
            clientPort: 5173,   // помогает в некоторых сетях/NAT
        },
        strictPort: true,     // если порт занят — сразу увидишь понятную ошибку
    },
})
