import { createApp } from 'vue';

import Settings from './Settings.vue';

window.addEventListener( 'DOMContentLoaded', () => {
	const el = document.getElementById( 'anwp-pg-app-settings' );

	if ( el ) {
		createApp( Settings ).mount( el );
	}
} );
