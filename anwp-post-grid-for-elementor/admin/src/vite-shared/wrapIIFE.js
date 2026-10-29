// Wraps every emitted Rollup chunk in `(function(){ ... })();` AFTER esbuild minification.
// Prevents esbuild-hoisted helper `var`s (e.g. `var wp = ...`) from leaking into the global
// scope and clobbering `window.wp` in WordPress admin context.
//
// Use as the LAST entry in vite plugins[]; generateBundle runs after esbuild renderChunk.
export function wrapIIFE() {
	return {
		name: 'wrap-iife',
		generateBundle( options, bundle ) {
			for ( const chunk of Object.values( bundle ) ) {
				if ( chunk.type === 'chunk' ) {
					chunk.code = `(function(){${ chunk.code }})();\n`;
				}
			}
		},
	};
}
