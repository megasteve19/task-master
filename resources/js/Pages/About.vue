<template>
	<Head title="About" />

	<AppLayout>
		<section class="grid gap-4">
			<h3>This project powered by:</h3>

			<Card>
				<p>Frontend:</p>
				<ul>
					<li>Vue 3</li>
					<li>Tailwind CSS</li>
					<li>Inertia.js</li>
					<li>Sortable.js</li>
					<li>Bootstrap Icons</li>
					<li>Feather Icons</li>
				</ul>
			</Card>

			<Card>
				<p>Backend:</p>
				<ul>
					<li>Laravel 10</li>
					<li>Laravel Scout</li>
					<li>Laravel Octane</li>
					<li>Laravel Prompts</li>
					<li>Typesense</li>
					<li>Swoole</li>
				</ul>
			</Card>

			<Card>
				<p>Big thanks to following dev tools:</p>
				<ul>
					<li>Clockwork</li>
					<li>Laravel IDE helper</li>
					<li>Laravel Sail</li>
				</ul>
			</Card>
		</section>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';
</script>
