<template>
	<Head title="Projects" />

	<AppLayout title="Projects">
		<div
			v-if="isRole('admin')"
			class="mb-2 text-end"
		>
			<Button
				variant="primary"
				@click="projectFormModal.openModal()"
			>
				New Project
			</Button>
		</div>
		<div class="flex flex-col gap-4 mb-4">
			<TextInput
				v-model="filters.search"
				placeholder="Search by name, description, or assignee..."
			/>

			<div class="flex justify-between">
				<div class="flex gap-4">
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
						:variant="filters.status === 'trashed' ? 'danger' : 'default'"
						@click="filters.status = 'trashed'"
					>
						Trashed
					</Button>
				</div>
			</div>
		</div>

		<section
			v-if="projects.data.length !== 0"
			class="flex flex-col gap-4"
		>
			<!-- Project Cards -->
			<Card
				v-for="project in projects.data"
				:key="project.id"
			>
				<div class="flex justify-between mb-2">
					<div class="flex items-center gap-4">
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
							<!-- Edit -->
							<DropdownButton
								v-if="!project.is_archived && !project.deleted_at"
								@click="projectFormModal.openModal(project)"
							>
								<PenIcon class="w-4 h-4" />
								Edit
							</DropdownButton>

							<!-- Archive -->
							<template v-if="!project.deleted_at">
								<DropdownButton
									v-if="!project.is_archived"
									variant="warning"
									@click="archiveProject.openModal(project.id)"
								>
									<ArchiveIcon class="w-4 h-4" />
									Archive
								</DropdownButton>

								<!-- Restore Archived -->
								<DropdownButton
									v-else
									variant="default"
									@click="restoreArchivedProject.openModal(project.id)"
								>
									<RestoreIcon class="w-4 h-4" />
									Restore
								</DropdownButton>
							</template>

							<!-- Delete -->
							<DropdownButton
								v-if="!project.deleted_at"
								variant="danger"
								@click="deleteProject.openModal(project.id)"
							>
								<TrashIcon class="w-4 h-4" />
								Delete
							</DropdownButton>
							<template v-else>
								<!-- Restore Deleted -->
								<DropdownButton
									variant="default"
									@click="restoreDeletedProject.openModal(project.id)"
								>
									<RestoreIcon class="w-4 h-4" />
									Restore
								</DropdownButton>

								<!-- Permanently Delete -->
								<DropdownButton
									variant="danger"
									@click="permanentlyDeleteProject.openModal(project.id)"
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
		</section>

		<p
			class="text-center text-gray-400"
			v-else
		>No projects found.</p>
	</AppLayout>

	<!-- Delete project modal. -->
	<Modal :show="deleteProject.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to delete this project? This action can be undone.</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="deleteProject.closeModal()"
			>Cancel</Button>
			<Button
				variant="danger"
				@click="deleteProject.confirmDelete()"
			>Delete</Button>
		</div>
	</Modal>

	<!-- Restore deleted project modal. -->
	<Modal :show="restoreDeletedProject.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to restore this project?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="restoreDeletedProject.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="restoreDeletedProject.confirmRestore()"
			>Restore</Button>
		</div>
	</Modal>

	<!-- Permanently delete project modal. -->
	<Modal :show="permanentlyDeleteProject.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to permanently delete this project? This <u><strong>action cannot be undone</strong></u>.
		</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="permanentlyDeleteProject.closeModal()"
			>Cancel</Button>
			<Button
				variant="danger"
				@click="permanentlyDeleteProject.confirmDelete()"
			>Permanently Delete</Button>
		</div>
	</Modal>

	<!-- Archive project modal. -->
	<Modal :show="archiveProject.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to archive this project?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="archiveProject.closeModal()"
			>Cancel</Button>
			<Button
				variant="warning"
				@click="archiveProject.confirmArchive()"
			>Archive</Button>
		</div>
	</Modal>

	<!-- Restore archived project modal. -->
	<Modal :show="restoreArchivedProject.showModal">
		<h2>Are you sure?</h2>
		<p>Are you sure you want to restore this project?</p>
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="restoreArchivedProject.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="restoreArchivedProject.confirmRestore()"
			>Restore</Button>
		</div>
	</Modal>

	<!-- Project form modal. -->
	<Modal :show="projectFormModal.showModal">
		<h2 class="mb-2">{{ projectFormModal.edit ? 'Edit Project' : 'New Project' }}</h2>
		<form
			class="grid grid-cols-1 gap-2"
			@submit.prevent
		>
			<!-- Name -->
			<div>
				<InputLabel
					for="name"
					value="Project Name*"
				/>

				<TextInput
					id="name"
					v-model="projectForm.name"
					placeholder="Project Name"
					required
					autofocus
				/>

				<InputError :message="projectForm.errors.name" />
			</div>

			<!-- Description -->
			<div>
				<InputLabel
					for="description"
					value="Project Description"
				/>

				<TextArea
					id="description"
					v-model="projectForm.description"
					placeholder="Project Description"
				/>

				<InputError :message="projectForm.errors.description" />
			</div>

			<!-- Due Date -->
			<div>
				<InputLabel
					for="due_date"
					value="Due Date"
				/>

				<TextInput
					id="due_date"
					v-model="projectForm.due_date"
					type="date"
					placeholder="Due Date"
				/>

				<InputError :message="projectForm.errors.due_date" />
			</div>

			<!-- Assignees -->
			<div>
				<InputLabel value="Assignees" />

				<AvatarStack
					:users="projectForm.assignees"
					v-model:search="projectFormModal.userSearch"
					with-edit
					:search-results="users"
					@add="projectFormModal.addUser"
					@remove="projectFormModal.removeUser"
				/>
			</div>
		</form>

		<!-- Actions -->
		<div class="flex justify-end gap-2 mt-6">
			<Button
				variant="default"
				@click="projectFormModal.closeModal()"
			>Cancel</Button>
			<Button
				variant="primary"
				@click="projectFormModal.confirmSave()"
			>Save</Button>
		</div>
	</Modal>
</template>

<script setup>
import Card from '@/Components/Card.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AvatarStack from '@/Components/AvatarStack.vue';
import ClockIcon from '@/Components/Icons/Clock.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownButton from '@/Components/DropdownButton.vue';
import Button from '@/Components/Button.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import VerticalDotsIcon from '@/Components/Icons/VerticalDots.vue';
import PenIcon from '@/Components/Icons/Pen.vue';
import TrashIcon from '@/Components/Icons/Trash.vue';
import ArchiveIcon from '@/Components/Icons/Archive.vue';
import RestoreIcon from '@/Components/Icons/Restore.vue';
import { reactive } from 'vue';
import { router, useForm, Head } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import TextArea from '@/Components/TextArea.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import CardListIcon from '@/Components/Icons/CardList.vue';
import { isRole } from '@/helpers';

/**
 * Props of the page.
 */
const props = defineProps({
	projects: Object,
	users: {
		type: Array,
		default: [],
	},
});

/**
 * Filters for the projects.
 */
const filters = reactive({
	search: '',
	status: 'active',
});

// Reload the page when the filters change.
watch(() => {
	router.reload({
		data: filters,
	});
});

/**
 * Project delete related methods and properties.
 */
const deleteProject = reactive({
	showModal: false,
	projectId: null,

	/**
	 * Opens the delete project modal.
	 * 
	 * @param {String} projectId 
	 */
	openModal(projectId) {
		this.projectId = projectId;
		this.showModal = true;
	},

	/**
	 * Closes the delete project modal.
	 */
	closeModal() {
		this.projectId = null;
		this.showModal = false;
	},

	/**
	 * Performs the delete project action.
	 */
	confirmDelete() {
		router.delete(route('projects.destroy', this.projectId));
		this.closeModal();
	},
});

/**
 * Project permanently delete related methods and properties.
 */
const permanentlyDeleteProject = reactive({
	showModal: false,
	projectId: null,

	/**
	 * Opens the permanently delete project modal.
	 * 
	 * @param {String} projectId 
	 */
	openModal(projectId) {
		this.projectId = projectId;
		this.showModal = true;
	},

	/**
	 * Closes the permanently delete project modal.
	 */
	closeModal() {
		this.projectId = null;
		this.showModal = false;
	},

	/**
	 * Performs the permanently delete project action.
	 */
	confirmDelete() {
		router.delete(route('projects.destroy-permanently', this.projectId));
		this.closeModal();
	},
});

/**
 * Project restore deleted related methods and properties.
 */
const restoreDeletedProject = reactive({
	showModal: false,
	projectId: null,

	/**
	 * Opens the restore deleted project modal.
	 * 
	 * @param {String} projectId 
	 */
	openModal(projectId) {
		this.projectId = projectId;
		this.showModal = true;
	},

	/**
	 * Closes the restore deleted project modal.
	 */
	closeModal() {
		this.projectId = null;
		this.showModal = false;
	},

	/**
	 * Performs the restore deleted project action.
	 */
	confirmRestore() {
		router.put(route('projects.restore-delete', this.projectId));
		this.closeModal();
	},
});

/**
 * Project archive related methods and properties.
 */
const archiveProject = reactive({
	showModal: false,
	projectId: null,

	/**
	 * Opens the archive project modal.
	 * 
	 * @param {String} projectId 
	 */
	openModal(projectId) {
		this.projectId = projectId;
		this.showModal = true;
	},

	/**
	 * Closes the archive project modal.
	 */
	closeModal() {
		this.projectId = null;
		this.showModal = false;
	},

	/**
	 * Performs the archive project action.
	 */
	confirmArchive() {
		router.put(route('projects.archive', this.projectId));
		this.closeModal();
	},
});

/**
 * Project restore archived related methods and properties.
 */
const restoreArchivedProject = reactive({
	showModal: false,
	projectId: null,

	/**
	 * Opens the restore archived project modal.
	 * 
	 * @param {String} projectId 
	 */
	openModal(projectId) {
		this.projectId = projectId;
		this.showModal = true;
	},

	/**
	 * Closes the restore archived project modal.
	 */
	closeModal() {
		this.projectId = null;
		this.showModal = false;
	},

	/**
	 * Performs the restore archived project action.
	 */
	confirmRestore() {
		router.put(route('projects.restore-archive', this.projectId));
		this.closeModal();
	},
});

/**
 * Project form.
 */
const projectForm = useForm({
	id: '',
	name: '',
	description: '',
	due_date: '',
	assignees: [],
});

/**
 * Project form related methods and properties.
 */
const projectFormModal = reactive({
	showModal: false,
	isEditing: false,
	userSearch: '',

	// We have to join them because it seems like there'a bug in the Inertia's query array parameters.
	// It always keeps adding new parameters, and doesn't remove the old ones.
	// So, we have to join them and then split them in the controller.
	userExcept: computed(() => projectForm.assignees.map((assignee) => assignee.id).join(',')),

	/**
	 * Opens the project form modal.
	 * 
	 * @param {Object} project 
	 */
	openModal(project = null) {
		if (project) {
			projectForm.id = project.id;
			projectForm.name = project.name;
			projectForm.description = project.description;
			projectForm.due_date = project.due_date?.split('T')[0];
			projectForm.assignees = project.assignees;
			this.isEditing = true;
		}

		this.showModal = true;
	},

	/**
	 * Closes the project form modal.
	 */
	closeModal() {
		projectForm.id = '';
		projectForm.name = '';
		projectForm.description = '';
		projectForm.due_date = new Date().toISOString().split('T')[0];
		projectForm.assignees = [];
		this.isEditing = false;
		this.showModal = false;
		this.userSearch = '';
	},

	/**
	 * Performs the project form action.
	 */
	confirmSave() {
		const transformedForm = projectForm.transform((data) => ({
			...data,
			assignees: data.assignees.map((assignee) => assignee.id),
		}));

		if (this.isEditing) {
			transformedForm.put(route('projects.update', projectForm.id), {
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => this.closeModal(),
			});

			return;
		}

		transformedForm.post(route('projects.store'), {
			preserveScroll: true,
			preserveState: true,
			onSuccess: () => this.closeModal(),
		});
	},

	/**
	 * Adds a user to the assignees.
	 */
	addUser(user) {
		if (!user) {
			return;
		}

		projectForm.assignees.push(user);
		projectFormModal.userSearch = '';
	},

	/**
	 * Removes a user from the assignees.
	 */
	removeUser(user) {
		projectForm.assignees = projectForm.assignees.filter((assignee) => assignee.id !== user.id);
	},
});

watch([() => projectFormModal.userSearch, () => projectFormModal.userExcept], () => {
	router.reload({
		data: {
			userSearch: projectFormModal.userSearch,
			userExcept: projectFormModal.userExcept,
		},
		only: ['users'],
	});
});
</script>
