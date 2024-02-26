<template>
	<nav class="absolute top-0 left-0 flex flex-col gap-2 pr-2 transform -translate-x-full border-r-2">
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
			route="users.index"
			label="Users"
		>
			<PeopleIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="settings.show"
			label="Settings"
		>
			<GearIcon class="w-6 h-6" />
		</SidebarItem>

		<SidebarItem
			route="about"
			label="About"
		>
			<InfoIcon class="w-6 h-6" />
		</SidebarItem>
	</nav>
</template>

<script setup>
import SidebarItem from './SidebarItem.vue';

// Icons
import HomeIcon from './Icons/Home.vue';
import JournalIcon from './Icons/Journal.vue';
import CardListIcon from './Icons/CardList.vue';
import GearIcon from './Icons/Gear.vue';
import PeopleIcon from './Icons/People.vue';
import InfoIcon from './Icons/Info.vue';
</script>
