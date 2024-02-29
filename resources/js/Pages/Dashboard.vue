<template>
	<Head title="Dashboard" />

	<AppLayout :title="welcomeMessage">
		<!-- Widgets -->
		<section class="grid grid-cols-2 gap-4 mb-6">

			<!-- Time Widget -->
			<Widget
				gradient="from-violet-500 to-violet-300"
				header="Time"
				:value="time.value"
				description="Current time."
				class="col-span-2"
			/>

			<!-- Project Count Widget -->
			<Widget
				gradient="from-rose-500 to-rose-300"
				header="Projects"
				:value="projectCount"
				description="Total count of projects that you have been assigned to."
			/>

			<!-- Task Count Widget -->
			<Widget
				gradient="from-sky-500 to-sky-300"
				header="Tasks"
				:value="taskCount"
				description="Total count of tasks that you have been assigned to."
			/>
		</section>

		<!-- Projects -->
		<section class="flex flex-col gap-4">
			<div>
				<h3 class="mb2-">Your Projects</h3>
				<p class="text-gray-400">This is a list of all the projects that you have been assigned to, you may hop into
					any of them to see
					the details and tasks.</p>
			</div>

			<!-- Project Cards -->
			<ProjectCard
				v-if="projects.length !== 0"
				v-for="project in projects"
				:key="project.id"
				:project="project"
				@edit="project => projectFormModal.openModal(project)"
				@archive="project => archiveProject.openModal(project)"
				@restore-archived="project => restoreArchivedProject.openModal(project)"
				@delete="project => deleteProject.openModal(project)"
				@restore-deleted="project => restoreDeletedProject.openModal(project)"
				@permanently-delete="project => permanentlyDeleteProject.openModal(project)"
			/>

			<p
				class="mt-4 text-center text-gray-400"
				v-else
			>No projects found.</p>
		</section>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Widget from '@/Components/Widget.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import { reactive } from 'vue';
import { onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
	projectCount: Number,
	taskCount: Number,
	projects: Array,
});

const page = usePage();

/**
 * Welcome message.
 */
const welcomeMessage = computed(() => {
	const currentHour = new Date().getHours();
	const userName = page.props.auth.user.name.substring(0, page.props.auth.user.name.lastIndexOf(' '));

	switch (true) {
		case currentHour < 12 && currentHour >= 6:
			return `Good Morning, ${userName} ☀️`;
		case currentHour < 18 && currentHour >= 12:
			return `Good Afternoon, ${userName} 🌅`;
		case currentHour < 22 && currentHour >= 18:
			return `Good Evening, ${userName} 🌆`;
		case currentHour < 6:
			return `Good Night, ${userName} 🌙`;
	}
});

/**
 * Time state.
 */
const time = reactive({
	/**
	 * The interval, just need to keep track of it so we can clear it when the component is unmounted.
	 */
	interval: null,

	/**
	 * The current time.
	 */
	value: '',

	/**
	 * Updates the time.
	 */
	update() {
		const now = new Date();
		this.value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
	},
});

/**
 * Updates the time every second.
 */
onMounted(() => {
	time.update();
	time.interval = setInterval(() => time.update(), 1000);
});

/**
 * Clears the interval when the component is unmounted.
 */
onBeforeUnmount(() => {
	clearInterval(time.interval);
});
</script>