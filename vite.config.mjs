import { createViteConfig } from "vite-config-factory";

const entries = {
    'css/modularity-municipal-calendar': './source/sass/modularity-municipal-calendar.scss',
};

export default createViteConfig(entries, {
    outDir: "assets/dist",
    manifestFile: "manifest.json",
});
