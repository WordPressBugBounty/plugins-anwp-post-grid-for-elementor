<template>
	<label class="anwp-pg-toggle" :style="cssVars">
		<input
			type="checkbox"
			class="anwp-pg-toggle__input"
			:checked="modelValue"
			:aria-label="ariaLabel || null"
			@change="$emit( 'update:modelValue', $event.target.checked )"
		>
		<span class="anwp-pg-toggle__track">
			<span class="anwp-pg-toggle__label anwp-pg-toggle__label--on">{{ onLabel }}</span>
			<span class="anwp-pg-toggle__label anwp-pg-toggle__label--off">{{ offLabel }}</span>
			<span class="anwp-pg-toggle__thumb"></span>
		</span>
	</label>
</template>

<script setup>
	import { computed } from 'vue';

	const props = defineProps( {
		modelValue: { type: Boolean, default: false },
		onLabel: { type: String, default: 'On' },
		offLabel: { type: String, default: 'Off' },
		ariaLabel: { type: String, default: '' },
		color: { type: String, default: '#82C7EB' },
		width: { type: Number, default: 90 },
		height: { type: Number, default: 30 },
		fontSize: { type: Number, default: 12 },
	} );

	defineEmits( [ 'update:modelValue' ] );

	const cssVars = computed( () => ( {
		'--anwp-pg-toggle-width': `${ props.width }px`,
		'--anwp-pg-toggle-height': `${ props.height }px`,
		'--anwp-pg-toggle-font-size': `${ props.fontSize }px`,
		'--anwp-pg-toggle-color': props.color,
	} ) );
</script>

<style scoped>
	.anwp-pg-toggle {
		position: relative;
		display: inline-block;
		width: var( --anwp-pg-toggle-width );
		height: var( --anwp-pg-toggle-height );
		cursor: pointer;
		user-select: none;
	}

	.anwp-pg-toggle__input {
		position: absolute;
		width: 0;
		height: 0;
		margin: 0;
		opacity: 0;
	}

	.anwp-pg-toggle__track {
		position: absolute;
		inset: 0;
		border-radius: calc( var( --anwp-pg-toggle-height ) / 2 );
		background-color: #bfcbd9;
		transition: background-color 0.2s ease;
	}

	.anwp-pg-toggle__input:checked ~ .anwp-pg-toggle__track {
		background-color: var( --anwp-pg-toggle-color );
	}

	.anwp-pg-toggle__input:focus-visible ~ .anwp-pg-toggle__track {
		box-shadow: 0 0 0 2px rgba( 130, 199, 235, 0.7 );
	}

	.anwp-pg-toggle__thumb {
		position: absolute;
		top: 2px;
		left: 2px;
		width: calc( var( --anwp-pg-toggle-height ) - 4px );
		height: calc( var( --anwp-pg-toggle-height ) - 4px );
		border-radius: 50%;
		background-color: #fff;
		box-shadow: 0 1px 3px rgba( 0, 0, 0, 0.4 );
		transition: transform 0.2s ease;
	}

	.anwp-pg-toggle__input:checked ~ .anwp-pg-toggle__track .anwp-pg-toggle__thumb {
		transform: translateX( calc( var( --anwp-pg-toggle-width ) - var( --anwp-pg-toggle-height ) ) );
	}

	.anwp-pg-toggle__label {
		position: absolute;
		top: 0;
		line-height: var( --anwp-pg-toggle-height );
		font-size: var( --anwp-pg-toggle-font-size );
		color: #fff;
		white-space: nowrap;
		opacity: 0;
		transition: opacity 0.2s ease;
	}

	.anwp-pg-toggle__label--on {
		left: 0;
		padding-left: 10px;
	}

	.anwp-pg-toggle__label--off {
		right: 0;
		padding-right: 10px;
	}

	.anwp-pg-toggle__input:checked ~ .anwp-pg-toggle__track .anwp-pg-toggle__label--on {
		opacity: 1;
	}

	.anwp-pg-toggle__input:not( :checked ) ~ .anwp-pg-toggle__track .anwp-pg-toggle__label--off {
		opacity: 1;
	}
</style>
