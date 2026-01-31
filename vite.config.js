import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/hasil-pemantauan.css",
                "resources/css/hasil-pemantauan-popup.css",
                "resources/js/hasil-pemantauan.js",
                "resources/css/monitoring.css",
                "resources/js/detail-lokasi.js",
                "resources/css/detail-lokasi.css",
                "resources/css/accordion.css",
                "resources/css/admin/dashboard.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
