import { readdirSync } from "node:fs";
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...readdirSync("resources/css").filter((file) => file.endsWith(".css")).map((file) => "resources/css/" + file),
                "resources/js/app.js"
            ],
            refresh: true
        })
    ]
});
