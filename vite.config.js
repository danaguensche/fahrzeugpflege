import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import vuetify from 'vite-plugin-vuetify';

export default defineConfig({
    plugins: [
        vue(),
        vuetify({ autoImport: true }),
        laravel({
            input: ['resources/css/main.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources',
            'vuex': 'vuex/dist/vuex.esm-bundler.js',
        },
    },
    build: {
        outDir: 'public/build', 
        assetsDir: 'assets',
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
                entryFileNames: 'js/[name]-[hash].js',
                chunkFileNames: 'js/[name]-[hash].js',
            },
        },
    },

    // server: {
    //     cors: true,
    //     host: '172.17.100.242',
    //     port: 8082
    // },
});
