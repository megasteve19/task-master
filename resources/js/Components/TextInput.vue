<template>
	<input
		type="text"
		class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
		:class="{
			'px-2 py-1 text-sm': sm,
			'px-3 py-2': !sm,
		}"
		ref="input"
		v-model="model"
	/>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
	sm: {
		type: Boolean,
		default: false,
	},
});

const model = defineModel({
	type: String,
	required: true,
});

const input = ref(null);

onMounted(() => {
	if (input.value.hasAttribute('autofocus')) {
		input.value.focus();
	}
});

defineExpose({ focus: () => input.value.focus() });
</script>
