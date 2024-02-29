<template>
	<div class="relative w-full">
		<!-- Search input -->
		<TextInput
			v-model="search.query"
			class="w-full h-10"
			placeholder="Search for projects or tasks..."
			@focus="search.show = true"
			@blur="search.show = false"
		/>

		<Transition
			enter-active-class="duration-150 ease-out"
			enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
			enter-to-class="translate-y-0 opacity-100 sm:scale-100"
			leave-active-class="duration-100 ease-in"
			leave-from-class="translate-y-0 opacity-100 sm:scale-100"
			leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
		>
			<!-- Search results -->
			<div
				v-if="search.show && search.hasAny"
				class="absolute left-0 w-full mt-2 z-index-10 top-full"
			>
				<div class="flex flex-col gap-6 px-8 py-6 bg-white border rounded-lg shadow-sm">
					<!-- Projects -->
					<div
						class="flex flex-row gap-4"
						v-if="search.results?.projects.length > 0"
					>
						<div class="pt-1">
							<JournalIcon class="w-6 h-6 text-gray-400" />
						</div>
						<div>
							<h6 class="mb-1 text-gray-400">Projects</h6>

							<div class="flex flex-col gap-1">
								<div
									:key="project.id"
									v-for="project in search.results.projects"
								>
									<Link
										:href="route('projects.show', project)"
										class="mb-1 text-blue-500 hover:underline"
									>{{ project.name }}</Link>

									<p class="text-xs text-gray-400 line-clamp-2">{{ project.description }}</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Tasks -->
					<div
						class="flex flex-row gap-4"
						v-if="search.results?.tasks.length > 0"
					>
						<div class="pt-1">
							<CardListIcon class="w-6 h-6 text-gray-400" />
						</div>
						<div>
							<h6 class="mb-1 text-gray-400">Tasks</h6>

							<div class="flex flex-col gap-1">
								<div
									:key="task.id"
									v-for="task in search.results.tasks"
								>
									<Link
										:href="route('projects.tasks.index', {
											project: task.project_id,
											filters: {
												search: task.name,
												status: task.archived_at ? 'archived' : 'active'
											}
										})"
										class="mb-1 text-blue-500 hover:underline"
									>{{ task.name }}</Link>

									<p class="text-xs text-gray-400 line-clamp-2">{{ task.description }}</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Users -->
					<div
						class="flex flex-row gap-4"
						v-if="search.results?.users.length > 0"
					>
						<div class="pt-1">
							<PeopleIcon class="w-6 h-6 text-gray-400" />
						</div>
						<div>
							<h6 class="mb-4 text-gray-400">Users</h6>

							<div class="flex flex-col gap-2">
								<div
									class="flex flex-row gap-2"
									:key="user.id"
									v-for="user in search.results.users"
								>
									<img
										:src="user.avatar"
										:alt="user.name"
										class="w-8 h-8 rounded-full"
									>

									<Link
										:href="route('users.index')"
										class="mb-1 text-blue-500 hover:underline"
									>{{ user.name }}</Link>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</Transition>
	</div>
</template>

<script setup>
import { reactive } from 'vue';
import { watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

// Components
import TextInput from './TextInput.vue';

// Icons
import JournalIcon from './Icons/Journal.vue';
import CardListIcon from './Icons/CardList.vue';
import PeopleIcon from './Icons/People.vue';

/**
 * Search state.
 */
const search = reactive({
	/**
	 * Query of the search.
	 */
	query: '',

	/**
	 * Results of the search.
	 */
	results: {
		projects: [],
		tasks: [],
		users: [],
	},

	/**
	 * Whether the search results should be shown.
	 */
	show: false,

	/**
	 * Whether the search has any results.
	 */
	hasAny: computed(() => {
		for (const key in search.results) {
			if (search.results[key].length > 0) {
				return true;
			}
		}

		return false;
	}),

	/**
	 * Perform the search.
	 */
	async perform() {
		const response = await fetch(route('api.search.global', {
			query: this.query
		}), { headers: { 'accept': 'application/json' } });

		this.results = (await response.json()).data;
	},

	/**
	 * Reset the search.
	 */
	reset() {
		this.query = '';
		this.results = {
			projects: [],
			tasks: [],
			users: [],
		};
	}
});

watch(() => search.query, () => {
	if (search.query.length === 0) {
		search.reset();
		return;
	}

	search.perform();
});
</script>