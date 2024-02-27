<template>
	<button
		type="button"
		class="flex flex-row items-center w-full gap-3 px-4 py-1.5 text-sm font-medium text-gray-700 transition duration-150 ease-in-out whitespace-nowrap hover:bg-gray-100 focus:bg-gray-100"
		:class="classes"
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
		'default': 'text-gray-700 hover:bg-gray-100 focus:bg-gray-100',
		'danger': 'text-red-600 hover:bg-red-50 focus:bg-red-50',
		'warning': 'text-orange-600 hover:bg-orange-50 focus:bg-orange-50',
	}[props.variant];
});
</script>
