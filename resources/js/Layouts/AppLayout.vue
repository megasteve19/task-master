<template>
	<header class="relative z-10 w-full h-16 bg-white shadow-sm">
		<div class="flex h-full max-w-screen-md gap-4 px-4 mx-auto">
			<div class="flex items-center w-full">
				<GlobalSearch />
			</div>
			<div class="flex items-center justify-end">
				<Dropdown>
					<template #trigger>
						<img
							class="object-cover w-10 h-10 cursor-pointer rounded-xl aspect-square"
							:src="user.avatar"
							alt="Avatar of user"
						>
					</template>

					<template #content>
						<DropdownLink :href="route('profile.edit')">
							<PersonIcon class="w-4 h-4" />
							Profile
						</DropdownLink>
						<DropdownLink
							variant="danger"
							:href="route('logout')"
							method="post"
							as="button"
						>
							<LogoutIcon class="w-4 h-4" />
							Logout
						</DropdownLink>
					</template>
				</Dropdown>
			</div>
		</div>
	</header>

	<div class="relative max-w-screen-md mx-auto mt-12 overflow-visible">
		<Sidebar />

		<main class="w-full px-4">
			<h2
				v-if="title"
				class="mb-4 text-2xl font-semibold"
			>{{ title }}</h2>

			<slot />
		</main>

		<footer class="py-4">
			<p class="text-center text-gray-300">
				2024 ~ Abdulkadir Cemiloğlu
			</p>
		</footer>
	</div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

// Components
import Sidebar from '@/Components/Sidebar.vue';
import GlobalSearch from '@/Components/GlobalSearch.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// Icons
import LogoutIcon from '@/Components/Icons/Logout.vue';
import PersonIcon from '@/Components/Icons/Person.vue';

const props = defineProps({
	title: String,
});

const page = usePage();

const user = computed(() => page.props.auth.user);

const search = ref('');
</script>
