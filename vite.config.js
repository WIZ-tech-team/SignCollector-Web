import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue()
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': fileURLToPath(new URL("./resources/vue-app", import.meta.url))
        }
    },
    // server: {
    //     // cors: {
    //     //   origin: [
    //     //     'https://aa88-41-37-128-76.ngrok-free.app', // Your ngrok URL
    //     //     'http://localhost:5173' // Local dev
    //     //   ],
    //     //   credentials: true
    //     // }
    //     hmr: {
    //         host: 'localhost',
    //     }
    // }
});
