<template>
	<div class="relative">
		<div @click="open = !open" :class="triggerClasses">
			<slot name="trigger" />
		</div>

		<!-- Full Screen Dropdown Overlay -->
		<div
			v-show="open"
			class="fixed inset-0 z-40"
			@click="open = false"
		></div>

		<Transition
			enter-active-class="transition duration-200 ease-out"
			enter-from-class="scale-95 opacity-0"
			enter-to-class="scale-100 opacity-100"
			leave-active-class="transition duration-75 ease-in"
			leave-from-class="scale-100 opacity-100"
			leave-to-class="scale-95 opacity-0"
		>
			<div
				v-show="open"
				class="absolute z-50 w-auto mt-2 rounded-lg shadow-lg"
				:class="[alignmentClasses]"
				@click="closeOnContentClick ? open = false : null"
			>
				<div
					class="rounded-lg ring-1 ring-black ring-opacity-5"
					:class="contentClasses"
				>
					<slot name="content" />
				</div>
			</div>
		</Transition>
	</div>
</template>


<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
	align: {
		type: String,
		default: 'right',
	},
	contentClasses: {
		type: String,
		default: 'py-2 bg-white',
	},
	triggerClasses: {
		type: String,
		default: '',
	},
	closeOnContentClick: {
		type: Boolean,
		default: true,
	},
});

const closeOnEscape = (e) => {
	if (open.value && e.key === 'Escape') {
		open.value = false;
	}
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const alignmentClasses = computed(() => {
	if (props.align === 'left') {
		return 'ltr:origin-top-left rtl:origin-top-right start-0';
	} else if (props.align === 'right') {
		return 'ltr:origin-top-right rtl:origin-top-left end-0';
	} else if (props.align === 'center') {
		return 'origin-top-center left-1/2 transform -translate-x-1/2';
	}
	 else {
		return 'origin-top';
	}
});

const open = ref(false);

const close = () => {
	open.value = false;
};

defineExpose({ close });
</script>
