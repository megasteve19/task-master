<template>
	<header class="grid w-full h-16 grid-cols-3 bg-white shadow-sm">
		<div></div>
		<div></div>
		<div></div>
	</header>

	<div class="relative max-w-screen-md mx-auto mt-12 overflow-visible">
		<Sidebar :user="auth.user" />

		<main class="w-full px-4">
			<h2
				v-if="title"
				class="mb-4 text-2xl font-semibold"
			>{{ title }}</h2>

			<slot />
		</main>
	</div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
	title: String,
});

const page = usePage();

const auth = computed(() => page.props.auth);
</script>
