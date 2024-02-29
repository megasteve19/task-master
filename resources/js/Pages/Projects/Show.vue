<template>
	<Head title="Project" />

	<AppLayout title="Project Information">
		<WarningCard
			v-if="project.archived_at"
			class="mb-4"
		>
			<p>
				<strong>Warning:</strong> This project has been archived and neither new tasks can be added nor project
				details can be updated.
			</p>
		</WarningCard>

		<Card class="mb-4">
			<h3 class="mb-2">{{ project.name }}</h3>
			<p class="mb-2">{{ project.description }}</p>
			<div class="text-end">
				<ButtonLink
					class="inline-flex items-center gap-1"
					variant="default"
					:href="route('projects.tasks.index', project)"
				>
					<span>See Tasks</span>
					<ArrowRightIcon class="w-4 h-4" />
				</ButtonLink>
			</div>
		</Card>

		<section class="grid grid-cols-2 gap-4 mb-4">
			<Widget
				header="Due Date"
				:value="project.due_date ? new Date(project.due_date).toLocaleDateString() : 'N/A'"
				description="The date when the project is due."
				:gradient="project.is_overdue ? 'from-orange-500 to-orange-300' : 'from-emerald-500 to-emerald-300'"
			/>

			<Widget
				header="Assignees"
				:value="project.assignees_count"
				description="Total count of assignees for this project."
				gradient="from-lime-500 to-lime-300"
			/>

			<Widget
				header="Task Status"
				:value="`${project.completed_tasks_count}/${project.tasks_count}`"
				description="Total count of completed tasks and total tasks."
				gradient="from-sky-500 to-sky-300"
			/>

			<Widget
				header="Task Progress"
				:value="taskProgress"
				description="The progress of the tasks in this project."
				gradient="from-violet-500 to-violet-300"
			/>

			<Widget
				header="Your Tasks"
				:value="project.assignee_tasks_count"
				description="Total count of tasks that you have been assigned to."
				gradient="from-rose-500 to-rose-300"
				class="col-span-2"
			/>
		</section>

		<section class="flex flex-col gap-4">
			<h3>Assignees</h3>

			<Card
				v-if="project.assignees.length > 0"
				class="flex gap-4"
				v-for="user in project.assignees"
			>
				<img
					:src="user.avatar"
					:alt="user.name"
					class="w-16 h-16 rounded-full"
				/>
				<div>
					<h4>{{ user.name }}</h4>
					<p><a
							:href="`mailto:${user.email}`"
							class="text-blue-500 hover:underline"
						>{{ user.email }}</a></p>
				</div>
			</Card>
			<p
				v-else
				class="text-center text-gray-400"
			>
				No one has been assigned to this project.
			</p>
		</section>
	</AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

// Components
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Widget from '@/Components/Widget.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

// Icons
import ArrowRightIcon from '@/Components/Icons/ArrowRight.vue';
import WarningCard from '@/Components/WarningCard.vue';

const props = defineProps({
	project: Object,
});

const taskProgress = computed(() => {
	if (props.project.tasks_count === 0 || props.project.completed_tasks_count === 0) {
		return 'N/A';
	}

	return `${Math.round((props.project.completed_tasks_count / props.project.tasks_count) * 100)}%`;
});
</script>
