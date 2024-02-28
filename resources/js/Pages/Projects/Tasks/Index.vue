<template>
	<Head title="Tasks" />

	<AppLayout>
		<WarningCard
			v-if="props.project.archived_at"
			class="mb-4"
		>
			<p>
				<strong>Warning:</strong> This project has been archived and no new tasks can be added.
			</p>
		</WarningCard>

		<div
			v-if="!props.project.archived_at"
			class="mb-2 text-end"
		>
			<Button
				@click="taskFormModal.openModal()"
				variant="primary"
			>
				New Task
			</Button>
		</div>
		<div class="flex flex-col gap-4 mb-4">
			<TextInput
				v-model="filters.search"
				placeholder="Search by name, description, status, or assignee..."
			/>

			<div class="flex justify-between">
				<div>
					<AvatarStack
						searchable
						v-model="filters.assignees"
					/>
				</div>
				<div class="flex gap-4">
					<Button
						v-if="!props.project.archived_at"
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
						v-if="!props.project.archived_at"
						:variant="filters.status === 'trashed' ? 'danger' : 'default'"
						@click="filters.status = 'trashed'"
					>
						Trashed
					</Button>
				</div>
			</div>
		</div>

		<div v-if="filters.status === 'active'">
			<!-- Todos -->
			<TaskGroup
				title="To do"
				:project="props.project"
				:tasks="todo"
				@edit="(task) => taskFormModal.openModal(task)"
				@status-updated="(task, status) => taskUpdateStatus(task, status)"
				@delete="(task) => deleteTask.openModal(task)"
				@archive="(task) => archiveTask.openModal(task)"
			/>

			<!-- In Progress -->
			<TaskGroup
				title="In Progress"
				:project="props.project"
				:tasks="inProgress"
				@edit="(task) => taskFormModal.openModal(task)"
				@status-updated="(task, status) => taskUpdateStatus(task, status)"
				@delete="(task) => deleteTask.openModal(task)"
				@archive="(task) => archiveTask.openModal(task)"
			/>

			<!-- Completed -->
			<TaskGroup
				title="Completed"
				:project="props.project"
				:tasks="completed"
				@edit="(task) => taskFormModal.openModal(task)"
				@status-updated="(task, status) => taskUpdateStatus(task, status)"
				@delete="(task) => deleteTask.openModal(task)"
				@archive="(task) => archiveTask.openModal(task)"
			/>
		</div>
		<!-- Archived or deleted -->
		<section
			v-else
			class="flex flex-col gap-4"
		>
			<div
				v-if="props.tasks.length > 0"
				ref="completedList"
				class="grid grid-cols-1 gap-4"
			>
				<TaskCard
					v-for="task in props.tasks"
					:key="task.id"
					:task="task"
					@restore-deleted="(task) => restoreDeletedTask.openModal(task)"
					@permanently-delete="(task) => permanentlyDeleteTask.openModal(task)"
					@restore-archived="(task) => restoreArchivedTask.openModal(task)"
					@delete="(task) => deleteTask.openModal(task)"
				/>
			</div>

			<p
				v-else
				class="text-center text-gray-500"
			>
				No tasks to found.
			</p>
		</section>
	</AppLayout>

	<!-- Task form modal. -->
	<Modal :show="taskFormModal.showModal">
		<h2 class="mb-2">{{ taskFormModal.edit ? 'Edit Task' : 'New Task' }}</h2>
		<form
			class="grid grid-cols-1 gap-2"
			@submit.prevent
		>
			<!-- Name -->
			<div>
				<InputLabel
					for="name"
					value="Task Name*"
				/>

				<TextInput
					id="name"
					v-model="taskForm.name"
					placeholder="Task Name"
					required
					autofocus
				/>

				<InputError :message="taskForm.errors.name" />
			</div>

			<!-- Description -->
			<div>
				<InputLabel
					for="description"
					value="Task Description"
				/>

				<TextArea
					id="description"
					v-model="taskForm.description"
					placeholder="Task Description"
				/>

				<InputError :message="taskForm.errors.description" />
			</div>

			<!-- Due Date -->
			<div>
				<InputLabel
					for="due_date"
					value="Due Date"
				/>

				<TextInput
					id="due_date"
					v-model="taskForm.due_date"
					type="date"
					placeholder="Due Date"
				/>

				<InputError :message="taskForm.errors.due_date" />
			</div>

			<!-- Assignees -->
			<div>
				<InputLabel value="Assignees" />

				<AvatarStack
					v-model="taskForm.assignees"
					searchable
				/>
			</div>
		</form>

		<!-- Actions -->
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="taskFormModal.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="taskFormModal.confirmSave()"
			>Save</Button>
		</div>
	</Modal>

	<!-- Delete task modal. -->
	<Modal :show="deleteTask.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to delete this task? This action can be undone.</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="deleteTask.closeModal()"
			>Cancel</Button>
			<Button
				variant="danger"
				@click="deleteTask.confirmDelete()"
			>Delete</Button>
		</div>
	</Modal>

	<!-- Restore deleted task modal. -->
	<Modal :show="restoreDeletedTask.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to restore this task?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="restoreDeletedTask.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="restoreDeletedTask.confirmRestore()"
			>Restore</Button>
		</div>
	</Modal>

	<!-- Permanently delete task modal. -->
	<Modal :show="permanentlyDeleteTask.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to permanently delete this task? This <u><strong>action cannot be undone</strong></u>.
		</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="permanentlyDeleteTask.closeModal()"
			>Cancel</Button>
			<Button
				variant="danger"
				@click="permanentlyDeleteTask.confirmDelete()"
			>Permanently Delete</Button>
		</div>
	</Modal>

	<!-- Archive task modal. -->
	<Modal :show="archiveTask.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to archive this task?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="archiveTask.closeModal()"
			>Cancel</Button>
			<Button
				variant="warning"
				@click="archiveTask.confirmArchive()"
			>Archive</Button>
		</div>
	</Modal>

	<!-- Restore archived task modal. -->
	<Modal :show="restoreArchivedTask.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to restore this task?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="restoreArchivedTask.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="restoreArchivedTask.confirmRestore()"
			>Restore</Button>
		</div>
	</Modal>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';

// Components
import TaskGroup from './Partials/TaskGroup.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AvatarStack from '@/Components/AvatarStack.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import TaskCard from '@/Components/TaskCard.vue';
import WarningCard from '@/Components/WarningCard.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
	project: {
		type: Object,
		required: true,
	},
	tasks: {
		type: Object,
		required: true,
	},
	filters: Object,
});

const filters = reactive({
	search: props.filters.search || '',
	assignees: [],
	status: props.filters.status,
});

// I know, it is better to sort the tasks in the backend,
// but since we grouped them in frontend, we need to sort them here.
const todo = computed(() => props.tasks.filter((task) => task.status === 'todo').sort((a, b) => b.priority - a.priority));
const todoList = ref();
const todoListSortable = ref();

const inProgress = computed(() => props.tasks.filter((task) => task.status === 'in_progress').sort((a, b) => b.priority - a.priority));
const inProgressList = ref();
const inProgressListSortable = ref();

const completed = computed(() => props.tasks.filter((task) => task.status === 'completed').sort((a, b) => b.priority - a.priority));
const completedList = ref();
const completedListSortable = ref();

watch(filters, () => {
	router.reload({
		data: {
			filters: {
				search: filters.search,
				assignees: filters.assignees.map((assignee) => assignee.id).join(','),
				status: filters.status,
			}
		}
	});
}, { deep: true });

const taskUpdateStatus = (task, status) => {
	router.put(
		route('projects.tasks.update-status', [props.project, task.id]),
		{ status },
		{
			preserveScroll: true,
			preserveState: true,
		}
	);
};

const taskForm = useForm({
	id: '',
	name: '',
	description: '',
	due_date: '',
	assignees: [],
});

const taskFormModal = reactive({
	showModal: false,
	isEditing: false,

	/**
	 * Opens the task form modal.
	 * 
	 * @param {Object} task 
	 */
	openModal(task = null) {
		if (task) {
			taskForm.id = task.id;
			taskForm.name = task.name;
			taskForm.description = task.description;
			taskForm.due_date = task.due_date?.split('T')[0];
			taskForm.assignees = task.assignees;
			this.isEditing = true;
		}

		this.showModal = true;
	},

	/**
	 * Closes the task form modal.
	 */
	closeModal() {
		taskForm.id = '';
		taskForm.name = '';
		taskForm.description = '';
		taskForm.due_date = new Date().toISOString().split('T')[0];
		taskForm.assignees = [];
		this.isEditing = false;
		this.showModal = false;
	},

	/**
	 * Performs the task form action.
	 */
	confirmSave() {
		const transformedForm = taskForm.transform((data) => ({
			...data,
			assignees: data.assignees.map((assignee) => assignee.id),
		}));

		if (this.isEditing) {
			transformedForm.put(route('projects.tasks.update', [props.project, taskForm.id]), {
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => this.closeModal(),
			});

			return;
		}

		transformedForm.post(route('projects.tasks.store', props.project), {
			preserveScroll: true,
			preserveState: true,
			onSuccess: () => this.closeModal(),
		});
	},
});

const deleteTask = reactive({
	showModal: false,
	taskId: null,

	/**
	 * Opens the delete task modal.
	 * 
	 * @param {String} task 
	 */
	openModal(task) {
		console.log(task);
		this.taskId = task.id;
		this.showModal = true;
	},

	/**
	 * Closes the delete task modal.
	 */
	closeModal() {
		this.taskId = null;
		this.showModal = false;
	},

	/**
	 * Performs the delete task action.
	 */
	confirmDelete() {
		router.delete(route('projects.tasks.destroy', [props.project, this.taskId]));
		this.closeModal();
	},
});

const restoreDeletedTask = reactive({
	showModal: false,
	taskId: null,

	/**
	 * Opens the restore deleted task modal.
	 */
	openModal(task) {
		this.taskId = task.id;
		this.showModal = true;
	},

	/**
	 * Closes the restore deleted task modal.
	 */
	closeModal() {
		this.taskId = null;
		this.showModal = false;
	},

	/**
	 * Performs the restore deleted task action.
	 */
	confirmRestore() {
		router.put(route('projects.tasks.restore-delete', [props.project, this.taskId]));
		this.closeModal();
	},
});

const permanentlyDeleteTask = reactive({
	showModal: false,
	taskId: null,

	/**
	 * Opens the permanently delete task modal.
	 * 
	 * @param {String} task 
	 */
	openModal(task) {
		this.taskId = task.id;
		this.showModal = true;
	},

	/**
	 * Closes the permanently delete task modal.
	 */
	closeModal() {
		this.taskId = null;
		this.showModal = false;
	},

	/**
	 * Performs the permanently delete task action.
	 */
	confirmDelete() {
		router.delete(route('projects.tasks.destroy-permanently', [props.project, this.taskId]));
		this.closeModal();
	},
});

const archiveTask = reactive({
	showModal: false,
	taskId: null,

	/**
	 * Opens the archive task modal.
	 */
	openModal(task) {
		this.taskId = task.id;
		this.showModal = true;
	},

	/**
	 * Closes the archive task modal.
	 */
	closeModal() {
		this.taskId = null;
		this.showModal = false;
	},

	/**
	 * Performs the archive task action.
	 */
	confirmArchive() {
		router.put(route('projects.tasks.archive', [props.project, this.taskId]));
		this.closeModal();
	},
});

const restoreArchivedTask = reactive({
	showModal: false,
	taskId: null,

	/**
	 * Opens the restore archived task modal.
	 */
	openModal(task) {
		this.taskId = task.id;
		this.showModal = true;
	},

	/**
	 * Closes the restore archived task modal.
	 */
	closeModal() {
		this.taskId = null;
		this.showModal = false;
	},

	/**
	 * Performs the restore archived task action.
	 */
	confirmRestore() {
		router.put(route('projects.tasks.restore-archive', [props.project, this.taskId]));
		this.closeModal();
	},
});
</script>
