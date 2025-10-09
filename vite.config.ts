import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

//.ziggy

//.Quasar
import { quasar } from '@quasar/vite-plugin';
import { fileURLToPath } from 'node:url';

export default defineConfig({
    // عدم نمایش خطای ;ند بودن اینترنت در کنسول دیباگ
    // server: {
    //     hmr: {
    //         // overlay: false,
    //         // timeout: 30000,
    //     },
    // },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // @quasar/plugin-vite options list:
        // https://github.com/quasarframework/quasar/blob/dev/vite-plugin/index.d.ts
        quasar({
            sassVariables: fileURLToPath(new URL('./resources/css/quasar-variables.sass', import.meta.url)),
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            // اضافه کردن alias برای نادیده گرفتن حساسیت به حروف
            '@/components': '/resources/js/components',
            '@/Components': '/resources/js/components', // هر دو به یک مسیر اشاره کنن
        },
    },
});
