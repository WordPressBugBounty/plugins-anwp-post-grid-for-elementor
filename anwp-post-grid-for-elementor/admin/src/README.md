# Admin settings app - source

Source of `admin/js/pg-premium-admin-vue.min.js` and its readable build `admin/js/pg-premium-admin-vue.js`:
Vue 3.5 single-file components, built with Vite 7.

Build from a folder holding the contents of this `src/` folder:

    npm install vue@^3.5.26 vite@^7.2.2 @vitejs/plugin-vue@^6.0.3
    npx vite build --config vite.config.admin.js                     # pg-premium-admin-vue.min.js
    npx vite build --config vite.config.admin.js --mode unminified   # pg-premium-admin-vue.js

Output goes to `./anwp-post-grid-for-elementor/admin/js/`, relative to that folder.
