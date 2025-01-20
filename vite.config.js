// vite.config.js

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.jsx",
                // Landlord assets
                "resources/css/landlord/app.css",
                "resources/css/landlord/rtl.css",
                // Tenant assets
                "resources/css/tenant/app.css",
                "resources/css/tenant/rtl.css",
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    landlord: ["resources/css/landlord/app.css"],
                    tenant: ["resources/css/tenant/app.css"],
                },
            },
        },
    },
    css: {
        preprocessorOptions: {
            sass: {
                api: "modern",
                charset: false,
                quietDeps: true,
                includePaths: [
                    path.resolve(__dirname, "node_modules"),
                ],
            },
            scss: {
                api: "modern",
                charset: false,
                quietDeps: true,
                includePaths: [
                    path.resolve(__dirname, "node_modules"),
                ],
            },
        },
    },
    resolve: {
        dedupe: ['slick-carousel'],
    },
});