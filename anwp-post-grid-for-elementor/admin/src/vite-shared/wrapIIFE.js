// Wraps every emitted chunk in `(function(){ ... })();` after minification.
// `strict: true` puts "use strict" at the top of the file; inside this wrapper it applies
// to our code only, even if a page optimizer concatenates scripts into one file.
//
// Use as the LAST entry in vite plugins[]; generateBundle runs after minification.
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
