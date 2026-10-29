import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

import { wrapIIFE } from './vite-shared/wrapIIFE.js';

// https://vitejs.dev/config/
// `--mode unminified` writes a readable twin (pg-premium-admin-vue.js) next to the .min.js (WP.org guideline 4).
export default defineConfig( ( { mode } ) => {
	const unminified = 'unminified' === mode;

	return {
		server: {
			headers: {
				'Access-Control-Allow-Origin': '*',
			},
		},
		base: './',
		plugins: [
			vue(),
			wrapIIFE(),
		],
		build:
			{
				target: 'es2020',
				minify: ! unminified,
				emptyOutDir: false,
				rollupOptions: {
					input: 'vuejs/admin/main.js',
					output: {
						format: 'umd',
						dir: './anwp-post-grid-for-elementor/admin/js',
						entryFileNames: unminified ? 'pg-premium-admin-vue.js' : 'pg-premium-admin-vue.min.js',
						assetFileNames: unminified ? 'pg-premium-admin-vue[extname]' : 'pg-premium-admin-vue.min[extname]',
					},
				},
			},
		resolve: {
			alias: {
				'@': fileURLToPath( new URL( './vuejs/admin', import.meta.url ) ),
			},
		},
	};
} );
