<template>
	<Card
		class="border-2 border-white border-dashed"
		:data-task-id="task.id"
	>
		<div class="flex justify-between mb-2">
			<div class="flex items-center gap-4">
				<!-- Archived At -->
				<div
					v-if="task.archived_at"
					class="flex items-center gap-2 text-xs text-orange-500"
				>
					<ArchiveIcon class="w-3.5 h-3.5" />
					{{ new Date(task.archived_at).toLocaleDateString() }}
				</div>

				<!-- Due Date -->
				<div
					class="flex items-center gap-2 text-xs"
					:class="{
						'text-gray-400': !task.is_overdue,
						'text-rose-500': task.is_overdue,
					}"
				>
					<ClockIcon class="w-3.5 h-3.5" />
					{{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A' }}
				</div>
			</div>

			<div class="flex items-center gap-2">
				<!-- Dropdown Actions -->
				<Dropdown trigger-classes="flex justify-center items-center">
					<template #trigger>
						<button class="text-gray-400 hover:text-gray-600">
							<VerticalDotsIcon class="w-4 h-4" />
						</button>
					</template>

					<template #content>
						<!-- Edit -->
						<DropdownButton
							v-if="!task.archived_at && !task.deleted_at"
							@click="emit('edit', task)"
						>
							<PenIcon class="w-4 h-4" />
							Edit
						</DropdownButton>

						<!-- Archive -->
						<template v-if="!task.deleted_at">
							<DropdownButton
								v-if="!task.archived_at"
								variant="warning"
								@click="emit('archive', task)"
							>
								<ArchiveIcon class="w-4 h-4" />
								Archive
							</DropdownButton>

							<!-- Restore Archived -->
							<DropdownButton
								v-else
								variant="default"
								@click="emit('restore-archived', task)"
							>
								<RestoreIcon class="w-4 h-4" />
								Restore
							</DropdownButton>
						</template>

						<!-- Delete -->
						<DropdownButton
							v-if="!task.deleted_at"
							variant="danger"
							@click="emit('delete', task)"
						>
							<TrashIcon class="w-4 h-4" />
							Delete
						</DropdownButton>
						<template v-else>
							<!-- Restore Deleted -->
							<DropdownButton
								variant="default"
								@click="emit('restore-deleted', task)"
							>
								<RestoreIcon class="w-4 h-4" />
								Restore
							</DropdownButton>

							<!-- Permanently Delete -->
							<DropdownButton
								variant="danger"
								@click="emit('permanently-delete', task)"
							>
								<TrashIcon class="w-4 h-4" />
								Permanently Delete
							</DropdownButton>
						</template>
					</template>
				</Dropdown>

				<!-- Drag Handle -->
				<div class="flex items-center justify-center w-6 h-6 text-gray-400 cursor-grab drag-handle">
					<MoveIcon class="w-4 h-4" />
				</div>
			</div>
		</div>

		<h3 class="mb-1">{{ task.name }}</h3>
		<p class="mb-1">{{ task.description }}</p>
		<div class="flex items-center justify-between">
			<!-- Assignees -->
			<AvatarStack :users="task.assignees" />

			<!-- Move to Dropdown -->
			<Dropdown
				v-if="!task.archived_at && !task.deleted_at"
				align="left"
			>
				<template #trigger>
					<Button
						class="flex items-center justify-center gap-1"
						variant="default"
					>
						<span>Move to</span>
						<BoxArrowUpRightIcon class="w-4 h-4" />
					</Button>
				</template>

				<template #content>
					<DropdownButton
						v-if="task.status !== 'todo'"
						@click="emit('status-updated', task, 'todo')"
						variant="default"
					>
						<HorizontalDotsIcon class="w-4 h-4" />
						<span>To do</span>
					</DropdownButton>
					<DropdownButton
						v-if="task.status !== 'in_progress'"
						@click="emit('status-updated', task, 'in_progress')"
						variant="primary"
					>
						<ArrowRepeatIcon class="w-4 h-4" />
						<span>In progress</span>
					</DropdownButton>
					<DropdownButton
						v-if="task.status !== 'completed'"
						@click="emit('status-updated', task, 'completed')"
						variant="success"
					>
						<CheckAllIcon class="w-4 h-4" />
						<span>Done</span>
					</DropdownButton>
				</template>
			</Dropdown>
		</div>
	</Card>
</template>

<script setup>
import Card from '@/Components/Card.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownButton from '@/Components/DropdownButton.vue';
import Button from './Button.vue';
import AvatarStack from './AvatarStack.vue';

// Icons
import ArchiveIcon from '@/Components/Icons/Archive.vue';
import ClockIcon from '@/Components/Icons/Clock.vue';
import PenIcon from '@/Components/Icons/Pen.vue';
import RestoreIcon from '@/Components/Icons/Restore.vue';
import TrashIcon from '@/Components/Icons/Trash.vue';
import VerticalDotsIcon from '@/Components/Icons/VerticalDots.vue';
import MoveIcon from '@/Components/Icons/Move.vue';
import BoxArrowUpRightIcon from '@/Components/Icons/BoxArrowUpRight.vue';
import HorizontalDotsIcon from '@/Components/Icons/HorizontalDots.vue';
import ArrowRepeatIcon from '@/Components/Icons/ArrowRepeat.vue';
import CheckAllIcon from '@/Components/Icons/CheckAll.vue';

const props = defineProps({
	task: {
		type: Object,
		required: true,
	},
});

const emit = defineEmits([
	'archive',
	'delete',
	'edit',
	'permanently-delete',
	'restore-archived',
	'restore-deleted',
	'status-updated',
]);
</script>
