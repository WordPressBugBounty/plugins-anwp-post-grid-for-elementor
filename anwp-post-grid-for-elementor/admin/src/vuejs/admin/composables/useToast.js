import { ref } from 'vue';

/*
|--------------------------------------------------------------------
| Lightweight toast notifications (replaces toastr).
|
| Module-level state so every caller shares the same toast stack and a
| single <ToastHost /> mounted once in the app renders them all.
|--------------------------------------------------------------------
*/
const toasts = ref( [] );
let counter = 0;

export function useToast() {

	function dismiss( id ) {
		toasts.value = toasts.value.filter( ( toast ) => toast.id !== id );
	}

	/**
	 * Push a toast.
	 *
	 * @param {string} type    One of: success | warning | error.
	 * @param {string} text    Message (may contain trusted admin HTML, e.g. <br>).
	 * @param {number} timeout Auto-dismiss delay in ms (0 = sticky).
	 * @return {number} Toast id.
	 */
	function notify( type, text, timeout = 5000 ) {
		const id = ++counter;

		toasts.value.push( { id, type, text } );

		if ( timeout > 0 ) {
			setTimeout( () => dismiss( id ), timeout );
		}

		return id;
	}

	return { toasts, notify, dismiss };
}
