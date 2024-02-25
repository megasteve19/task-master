<template>
	<Link
		class="flex flex-col items-center justify-center w-full gap-1 px-6 py-2 rounded-xl"
		:class="{
			'bg-blue-100 text-blue-500': isActive,
			'hover:bg-gray-100 text-gray-500': !isActive,
		}"
		:href="route(props.route)"
	>
	<div>
		<slot></slot>
	</div>
	<div class="text-xs font-bold">{{ label }}</div>
	</Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
	route: {
		type: String,
		required: true,
	},
	label: {
		type: String,
		required: true,
	},
});

const isActive = computed(() => route().current(props.route));
</script>