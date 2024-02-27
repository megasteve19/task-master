<template>
	<Card>
		<div class="flex justify-between mb-2">
			<div class="flex items-center gap-4">
				<!-- Archived At -->
				<div
					v-if="project.archived_at"
					class="flex items-center gap-2 text-xs text-orange-500"
				>
					<ArchiveIcon class="w-3.5 h-3.5" />
					{{ new Date(project.archived_at).toLocaleDateString() }}
				</div>

				<!-- Due Date -->
				<div
					class="flex items-center gap-2 text-xs"
					:class="{
						'text-gray-400': !project.is_overdue,
						'text-rose-500': project.is_overdue,
					}"
				>
					<ClockIcon class="w-3.5 h-3.5" />
					{{ project.due_date ? new Date(project.due_date).toLocaleDateString() : 'N/A' }}
				</div>

				<!-- Task Count -->
				<div class="flex items-center gap-2 text-xs text-gray-400">
					<CardListIcon class="w-3.5 h-3.5" />
					{{ project.tasks_count }} tasks
				</div>
			</div>

			<!-- Dropdown Actions -->
			<Dropdown v-if="isRole('admin')">
				<template #trigger>
					<button class="text-gray-400 hover:text-gray-600">
						<VerticalDotsIcon class="w-4 h-4" />
					</button>
				</template>

				<template #content>
					<DropdownLink :href="route('projects.show', project)">
						<EyeIcon class="w-4 h-4" />
						View
					</DropdownLink>

					<!-- Edit -->
					<DropdownButton
						v-if="!project.archived_at && !project.deleted_at"
						@click="emit('edit', project)"
					>
						<PenIcon class="w-4 h-4" />
						Edit
					</DropdownButton>

					<!-- Archive -->
					<template v-if="!project.deleted_at">
						<DropdownButton
							v-if="!project.archived_at"
							variant="warning"
							@click="emit('archive', project)"
						>
							<ArchiveIcon class="w-4 h-4" />
							Archive
						</DropdownButton>

						<!-- Restore Archived -->
						<DropdownButton
							v-else
							variant="default"
							@click="emit('restore-archived', project)"
						>
							<RestoreIcon class="w-4 h-4" />
							Restore
						</DropdownButton>
					</template>

					<!-- Delete -->
					<DropdownButton
						v-if="!project.deleted_at"
						variant="danger"
						@click="emit('delete', project)"
					>
						<TrashIcon class="w-4 h-4" />
						Delete
					</DropdownButton>
					<template v-else>
						<!-- Restore Deleted -->
						<DropdownButton
							variant="default"
							@click="emit('restore-deleted', project)"
						>
							<RestoreIcon class="w-4 h-4" />
							Restore
						</DropdownButton>

						<!-- Permanently Delete -->
						<DropdownButton
							variant="danger"
							@click="emit('permanently-delete', project)"
						>
							<TrashIcon class="w-4 h-4" />
							Permanently Delete
						</DropdownButton>
					</template>
				</template>
			</Dropdown>
		</div>

		<!-- Project Details -->
		<h3 class="mb-1">{{ project.name }}</h3>
		<p class="mb-1">{{ project.description }}</p>
		<AvatarStack :users="project.assignees" />
	</Card>
</template>

<script setup>
import { isRole } from '@/helpers';

// Components
import Card from './Card.vue';
import Dropdown from './Dropdown.vue';
import DropdownLink from './DropdownLink.vue';
import DropdownButton from './DropdownButton.vue';
import AvatarStack from './AvatarStack.vue';


// Icons
import VerticalDotsIcon from '@/Components/Icons/VerticalDots.vue';
import PenIcon from '@/Components/Icons/Pen.vue';
import TrashIcon from '@/Components/Icons/Trash.vue';
import ArchiveIcon from '@/Components/Icons/Archive.vue';
import RestoreIcon from '@/Components/Icons/Restore.vue';
import CardListIcon from '@/Components/Icons/CardList.vue';
import ClockIcon from '@/Components/Icons/Clock.vue';
import EyeIcon from '@/Components/Icons/Eye.vue';

const props = defineProps({
	project: Object,
	dropdown: Boolean,
});

const emit = defineEmits([
	'edit',
	'archive',
	'restore-archived',
	'delete',
	'restore-deleted',
	'permanently-delete',
]);
</script>
