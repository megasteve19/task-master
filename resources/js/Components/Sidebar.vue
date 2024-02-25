<template>
	<nav class="absolute top-0 left-0 flex flex-col gap-2 pr-2 transform -translate-x-full border-r-2">
		<SidebarItem
			route="profile.edit"
			label="Profile"
		>
			<img
				class="w-12 h-12 rounded-full"
				:src="user.avatar"
				alt="Avatar of user"
			>
		</SidebarItem>

		<SidebarItem
			route="dashboard"
			label="Dashboard"
		>
			<HomeIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="projects.index"
			label="Projects"
		>
			<JournalIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="tasks.index"
			label="Tasks"
		>
			<CardListIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="settings.show"
			label="Settings"
		>
			<GearIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="logout"
			label="Logout"
			as="button"
			method="post"
		>
			<LogoutIcon class="w-6 h-6" />
		</SidebarItem>
	</nav>
</template>

<script setup>
import SidebarItem from './SidebarItem.vue';

// Icons
import HomeIcon from './Icons/Home.vue';
import LogoutIcon from './Icons/Logout.vue';
import JournalIcon from './Icons/Journal.vue';
import CardListIcon from './Icons/CardList.vue';
import GearIcon from './Icons/Gear.vue';

const props = defineProps({
	user: {
		type: Object,
		required: true,
	},
});
</script>
