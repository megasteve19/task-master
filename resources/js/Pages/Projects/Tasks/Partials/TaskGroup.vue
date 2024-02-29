<template>
	<section class="flex flex-col gap-4 mb-8">
		<h3>{{ title }}</h3>

		<div
			v-if="tasks.length > 0"
			ref="tasksRef"
			class="grid grid-cols-1 gap-4"
		>
			<TaskCard
				v-for="task in tasks"
				:key="task.id"
				:project="project"
				:task="task"
				@edit="(task) => emit('edit', task)"
				@status-updated="(task, status) => emit('status-updated', task, status)"
				@archive="(task) => emit('archive', task)"
				@delete="(task) => emit('delete', task)"
			/>
		</div>

		<p
			v-if="tasks.length === 0"
			class="text-gray-500"
		>
			No tasks found.
		</p>
	</section>
</template>

<script setup>
import { Sortable } from 'sortablejs/modular/sortable.core.esm';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

import TaskCard from '@/Components/TaskCard.vue';
import { onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
	title: String,
	project: Object,
	tasks: Array,
});

const emit = defineEmits([
	'edit',
	'status-updated',
	'archive',
	'delete'
]);

const tasksRef = ref(null);
const sortableRef = ref(null);

onBeforeUnmount(() => {
	sortableRef.value?.destroy();
});

watch(tasksRef, () => {
	if (tasksRef.value === null) {
		sortableRef.value?.destroy();
		sortableRef.value = null;
		return;
	}

	sortableRef.value = new Sortable(tasksRef.value, {
		swap: true,
		swapClass: 'task-swapping',
		handle: '.drag-handle',
		animation: 250,
		ghostClass: 'task-dragging',
		onEnd: (event) => {
			const swapFrom = event.item.dataset.taskId;
			const swapTo = event.swapItem.dataset.taskId;

			if (swapFrom === swapTo) {
				return;
			}

			sortableRef.value.option('disabled', true);

			router.put(
				route('projects.tasks.swap-priority', [props.project, swapFrom]),
				{ swap_with_task_id: swapTo, },
				{
					preserveScroll: true,
					preserveState: true,
					onSuccess: () => sortableRef.value.option('disabled', false),
				}
			);
		},
	});
}, { immediate: true });
</script>
