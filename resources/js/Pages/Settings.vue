<template>
	<Head title="Settings" />

	<AppLayout>
		<WarningCard>
			<h3>Sorry, this page not implemented yet.</h3>
			<p>If implemented following features will be available:</p>
			<ul>
				<li>Language Preferences</li>
				<li>Timezone Preferences</li>
				<li>Dark Mode</li>
			</ul>
		</WarningCard>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import WarningCard from '@/Components/WarningCard.vue';
import { Head } from '@inertiajs/vue3';
</script>
