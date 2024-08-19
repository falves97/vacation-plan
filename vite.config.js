import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import {viteStaticCopy} from "vite-plugin-static-copy";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Copy the docs folder to the public folder. Only with 'vite build'.
        viteStaticCopy({
            targets: [
                {
                    src: 'docs/*.yaml',
                    dest: 'docs',
                }
            ]
        })
    ],
});
