<template>
	<Head title="Dashboard" />

	<AppLayout :title="welcomeMessage">
		<section class="grid grid-cols-2 gap-4">

			<Widget
				gradient="from-violet-500 to-violet-300"
				header="Time"
				:value="time"
				description="Current time."
				class="col-span-2"
			/>

			<Widget
				gradient="from-rose-500 to-rose-300"
				header="Projects"
				:value="projectCount"
				description="Total count of projects that you have been assigned to."
			/>

			<Widget
				gradient="from-sky-500 to-sky-300"
				header="Tasks"
				:value="taskCount"
				description="Total count of tasks that you have been assigned to."
			/>
		</section>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Widget from '@/Components/Widget.vue';

const props = defineProps({
	projectCount: Number,
	taskCount: Number,
});

const page = usePage();

const welcomeMessage = computed(() => {
	const currentHour = new Date().getHours();
	const userName = page.props.auth.user.name.split(' ').shift();

	if (currentHour < 12) {
		return `Good morning, ${userName} 🌅`;
	} else if (currentHour < 18) {
		return `Good afternoon, ${userName} 🌞`;
	} else {
		return `Good evening, ${userName} 🌙`;
	}
});

const time = computed(() => {
	const now = new Date();
	return `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
});
</script>