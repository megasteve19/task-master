<template>
	<Link
		:href="props.href"
		class="flex flex-row items-center w-full gap-3 px-4 py-1.5 text-sm font-medium transition duration-150 ease-in-out whitespace-nowrap"
		:class="classes"
	>
	<slot />
	</Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
	href: {
		type: String,
		required: false,
		default: '#',
	},
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
		'success': 'text-emerald-600 hover:bg-emerald-50 focus:bg-emerald-50',
		'primary': 'text-blue-600 hover:bg-blue-50 focus:bg-blue-50',
	}[props.variant];
});
</script>
