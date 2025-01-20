// vite.config.js

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.jsx"],
            refresh: true,
        }),
    ],
    server: {
        origin: 'http://localhost:5173',
        cors: true
    },
});
