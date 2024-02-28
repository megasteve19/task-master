<template>
	<Link
		:class="classes"
		class="px-4 py-2 text-sm rounded-lg"
	>
	<slot />
	</Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
	variant: {
		type: String,
		default: 'default',
	},
});

const classes = computed(() => {
	return {
		'default': 'text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-300',
		'warning': 'text-orange-500 bg-orange-100 hover:bg-orange-200 border border-orange-300',
		'danger': 'text-red-500 bg-red-100 hover:bg-red-200 border border-red-300',
		'primary': 'text-blue-500 bg-blue-100 hover:bg-blue-200 border border-blue-300',
		'success': 'text-emerald-500 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300',
	}[props.variant];
});
</script>
