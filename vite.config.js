// vite.config.js

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import { readFileSync } from "fs";
import { resolve } from "path";

// Read theme configuration
const themeConfig = JSON.parse(
    readFileSync(resolve(__dirname, "resources/theme/theme-config.json"), "utf-8")
);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.jsx",
                "resources/js/modern-dashboard.js",
                "resources/scss/tenant/app.scss"
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            "@theme": "/resources/theme",
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `
                    @import "@theme/scss/variables.scss";
                `,
            },
        },
    },
    server: {
        origin: "http://localhost:5173",
        cors: true,
    },
});
