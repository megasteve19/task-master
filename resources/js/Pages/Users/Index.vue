<template>
	<Head title="Users" />

	<AppLayout>
		<WarningCard>
			<h3>Sorry, this page not implemented yet.</h3>
			<p>If implemented following features will be available:</p>
			<ul>
				<li>View all users</li>
				<li>Search users</li>
				<li>View user details</li>
				<li>Edit user details</li>
				<li>Delete user</li>
			</ul>
			<p>For now, please contact your administrator for any user related tasks.</p>
		</WarningCard>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import WarningCard from '@/Components/WarningCard.vue';
import { Head } from '@inertiajs/vue3';
</script>
