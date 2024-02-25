<template>
	<app-layout title="Projects">
		<div class="flex flex-col gap-4 mb-4">
			<input
				v-model="filters.search"
				type="text"
				class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
				placeholder="Search by name, description, or assignee..."
			/>

			<div class="flex justify-between">
				<div class="flex gap-4">
					<Button
						:variant="filters.status === 'all' ? 'primary' : 'default'"
						@click="filters.status = 'all'"
					>
						All
					</Button>
					<Button
						:variant="filters.status === 'active' ? 'success' : 'default'"
						@click="filters.status = 'active'"
					>
						Active
					</Button>
					<Button
						:variant="filters.status === 'archived' ? 'warning' : 'default'"
						@click="filters.status = 'archived'"
					>
						Archived
					</Button>
					<Button
						:variant="filters.status === 'overdue' ? 'danger' : 'default'"
						@click="filters.status = 'overdue'"
					>
						Overdue
					</Button>
					<Button
						:variant="filters.status === 'trashed' ? 'danger' : 'default'"
						@click="filters.status = 'trashed'"
					>
						Trashed
					</Button>
				</div>

				<Button variant="primary">
					New Project
				</Button>
			</div>
		</div>

		<section class="flex flex-col gap-4">
			<Card
				v-for="project in projects.data"
				:key="project.id"
				:class="{
					'ring-2 ring-offset-2 ring-orange-300': project.is_archived && !project.deleted_at,
				}"
			>
				<div class="flex justify-between mb-2">
					<p
						class="flex items-center gap-2 text-xs"
						:class="{
							'text-gray-400': !project.is_overdue,
							'text-rose-500': project.is_overdue,
						}"
					>
						<ClockIcon class="inline-block w-3.5 h-3.5" />
						{{ project.due_date ? new Date(project.due_date).toLocaleDateString() : 'N/A' }}
					</p>

					<Dropdown>
						<template #trigger>
							<button class="text-gray-400 hover:text-gray-600">
								<VerticalDotsIcon class="w-4 h-4" />
							</button>
						</template>

						<template #content>
							<!-- Edit -->
							<DropdownLink
								v-if="!project.is_archived && !project.deleted_at"
								:href="'#'"
							>
								<PenIcon class="w-4 h-4" />
								Edit
							</DropdownLink>

							<!-- Archive -->
							<template v-if="!project.deleted_at">
								<DropdownLink
									v-if="!project.is_archived"
									variant="warning"
									:href="'#'"
								>
									<ArchiveIcon class="w-4 h-4" />
									Archive
								</DropdownLink>

								<DropdownLink
									v-if="project.is_archived"
									variant="default"
									:href="'#'"
								>
									<RestoreIcon class="w-4 h-4" />
									Restore
								</DropdownLink>
							</template>

							<!-- Delete -->
							<DropdownLink
								v-if="!project.deleted_at"
								variant="danger"
								:href="'#'"
							>
								<TrashIcon class="w-4 h-4" />
								Delete
							</DropdownLink>
							<template v-else>
								<DropdownLink
									variant="default"
									:href="'#'"
								>
									<RestoreIcon class="w-4 h-4" />
									Restore
								</DropdownLink>

								<DropdownLink
									variant="danger"
									:href="'#'"
								>
									<TrashIcon class="w-4 h-4" />
									Permanently Delete
								</DropdownLink>
							</template>
						</template>
					</Dropdown>
				</div>

				<h3 class="mb-1">{{ project.name }}</h3>
				<p class="mb-1">{{ project.description }}</p>
				<AvatarStack :users="project.assignees" />
			</Card>
		</section>
	</app-layout>
</template>

<script setup>
import Card from '@/Components/Card.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AvatarStack from '@/Components/AvatarStack.vue';
import ClockIcon from '@/Components/Icons/Clock.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Button from '@/Components/Button.vue';
import VerticalDotsIcon from '@/Components/Icons/VerticalDots.vue';
import PenIcon from '@/Components/Icons/Pen.vue';
import TrashIcon from '@/Components/Icons/Trash.vue';
import ArchiveIcon from '@/Components/Icons/Archive.vue';
import RestoreIcon from '@/Components/Icons/Restore.vue';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
	projects: Object,
});

const filters = reactive({
	search: '',
	status: 'active',
});

watch(() => {
	router.reload({
		data: filters,
	});
});
</script>
