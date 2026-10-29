<template>
	<Teleport to="body">
		<div class="anwp-pg-toast-host">
			<TransitionGroup name="anwp-pg-toast">
				<div
					v-for="toast in toasts"
					:key="toast.id"
					class="anwp-pg-toast"
					:class="`anwp-pg-toast--${ toast.type }`"
					role="alert"
					@click="dismiss( toast.id )"
				>
					<span v-html="toast.text"></span>
				</div>
			</TransitionGroup>
		</div>
	</Teleport>
</template>

<script setup>
	import { useToast } from '../composables/useToast';

	const { toasts, dismiss } = useToast();
</script>

<style scoped>
	.anwp-pg-toast-host {
		position: fixed;
		top: 40px;
		right: 16px;
		z-index: 100100;
		display: flex;
		flex-direction: column;
		gap: 8px;
		max-width: 360px;
		pointer-events: none;
	}

	.anwp-pg-toast {
		pointer-events: auto;
		padding: 12px 16px;
		border-radius: 4px;
		color: #fff;
		font-size: 14px;
		line-height: 1.4;
		box-shadow: 0 2px 8px rgba( 0, 0, 0, 0.25 );
		cursor: pointer;
		word-break: break-word;
	}

	.anwp-pg-toast--success {
		background-color: #51a351;
	}

	.anwp-pg-toast--warning {
		background-color: #f89406;
	}

	.anwp-pg-toast--error {
		background-color: #bd362f;
	}

	/* Enter / leave transitions */
	.anwp-pg-toast-enter-active,
	.anwp-pg-toast-leave-active {
		transition: opacity 0.25s ease, transform 0.25s ease;
	}

	.anwp-pg-toast-enter-from,
	.anwp-pg-toast-leave-to {
		opacity: 0;
		transform: translateX( 20px );
	}
</style>
