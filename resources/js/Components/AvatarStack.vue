<template>
	<div class="flex -space-x-4">
		<div
			v-for="user in users"
			class="relative group"
		>
			<img
				:key="user.id"
				:src="user.avatar"
				:alt="user.name"
				class="w-8 h-8 border border-white rounded-full shadow-sm"
			/>

			<div
				class="absolute hidden w-auto px-2 py-1 mb-1 text-xs -translate-x-1/2 bg-white border rounded-lg shadow-sm animate-pop left-1/2 bottom-full group-hover:block whitespace-nowrap">
				{{ user.name }}
			</div>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	users: Array,
});
</script>