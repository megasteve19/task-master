<template>
	<textarea
		type="text"
		class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
		ref="textarea"
		v-model="model"
	></textarea>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
	type: String,
	required: true,
});

const textarea = ref(null);

onMounted(() => {
	if (textarea.value.hasAttribute('autofocus')) {
		textarea.value.focus();
	}
});

defineExpose({ focus: () => textarea.value.focus() });
</script>
