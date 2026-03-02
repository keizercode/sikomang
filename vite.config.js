import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",

                "resources/css/admin.css",
                "resources/js/admin.js",

                "resources/css/hasil-pemantauan.css",
                "resources/css/hasil-pemantauan-popup.css",
                "resources/js/hasil-pemantauan.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
