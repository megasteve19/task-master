<template>
	<div
		:class="[gradient]"
		class="p-4 text-white shadow-sm rounded-xl bg-gradient-to-t"
	>
		<h3 class="mb-2 text-xl font-semibold">{{ header }}</h3>
		<p class="text-4xl font-bold ">{{ value }}</p>
		<p
			v-if="description"
			class="text-xs"
		>{{ description }}</p>
	</div>
</template>

<script setup>
const props = defineProps({
	gradient: String,
	header: String,
	value: [String, Number],
	description: String,
});
</script>
