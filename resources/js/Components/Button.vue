<template>
	<button
		:class="classes"
		class="px-4 py-2 text-sm rounded-lg"
	>
		<slot />
	</button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	variant: {
		type: String,
		default: 'default',
	},
});

const classes = computed(() => {
	return {
		'default': 'text-gray-600 bg-gray-100 hover:bg-gray-200',
		'warning': 'text-orange-500 bg-orange-100 hover:bg-orange-200',
		'danger': 'text-red-500 bg-red-100 hover:bg-red-200',
		'primary': 'text-blue-500 bg-blue-100 hover:bg-blue-200',
		'success': 'text-emerald-500 bg-emerald-100 hover:bg-emerald-200',
	}[props.variant];
});
</script>
