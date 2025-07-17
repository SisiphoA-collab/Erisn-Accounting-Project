import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify';

export default defineConfig({
    plugins: [
        vue({ template: { transformAssetUrls } }),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vuetify({ autoImport: true }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
    watch: {
      ignored: [
        '**/vendor/**',
        '**/storage/**',
        '**/routes/**', // prevent PHP files from triggering reloads
      ]
    }
  }
});
