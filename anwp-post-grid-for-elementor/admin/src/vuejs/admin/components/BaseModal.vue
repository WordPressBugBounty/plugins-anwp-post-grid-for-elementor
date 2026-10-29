<template>
	<dialog
		ref="dialogEl"
		class="anwp-pg-modal"
		@cancel="onCancel"
		@click="onBackdropClick"
	>
		<div class="anwp-pg-modal__box">
			<div v-if="$slots.header" class="anwp-pg-modal__header">
				<slot name="header"></slot>
			</div>
			<div class="anwp-pg-modal__content">
				<slot></slot>
			</div>
			<div v-if="$slots.footer" class="anwp-pg-modal__footer">
				<slot name="footer"></slot>
			</div>
		</div>
	</dialog>
</template>

<script setup>
	import { ref, useSlots } from 'vue';

	const props = defineProps( {
		// Mirrors Modaal `is_locked`: when false, ESC and backdrop clicks do NOT close the modal.
		dismissable: {
			type: Boolean,
			default: false,
		},
	} );

	useSlots();

	const dialogEl = ref( null );

	function open() {
		dialogEl.value?.showModal();
	}

	function close() {
		dialogEl.value?.close();
	}

	function onCancel( event ) {
		// `cancel` fires on ESC press.
		if ( ! props.dismissable ) {
			event.preventDefault();
		}
	}

	function onBackdropClick( event ) {
		// A click on the ::backdrop reports the <dialog> element as the target.
		if ( props.dismissable && event.target === dialogEl.value ) {
			close();
		}
	}

	defineExpose( { open, close } );
</script>

<style scoped>
	.anwp-pg-modal {
		padding: 0;
		border: none;
		border-radius: 4px;
		max-width: min( 500px, calc( 100vw - 40px ) );
		width: 100%;
		box-shadow: 0 5px 30px rgba( 0, 0, 0, 0.3 );
		background: #fff;
	}

	.anwp-pg-modal::backdrop {
		background: rgba( 0, 0, 0, 0.55 );
	}

	.anwp-pg-modal__header {
		border-bottom: 1px solid #eee;
		color: #23282d;
		font-size: 14px;
		line-height: 1.2;
		padding: 15px;
	}

	.anwp-pg-modal__content {
		padding: 10px 15px;
		max-height: calc( 100vh - 180px );
		overflow-y: auto;
	}

	.anwp-pg-modal__footer {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		gap: 8px;
		border-top: 1px solid #e4e6e8;
		padding: 12px 15px;
		background: #f6f7f7;
	}
</style>
