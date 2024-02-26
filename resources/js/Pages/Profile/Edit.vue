<template>
	<Head title="Profile" />

	<AppLayout>
		<template #header>
			<h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
		</template>

		<div>
			<div class="mx-auto space-y-6 max-w-7xl">
				<Card>
					<UpdateProfileInformationForm
						:must-verify-email="mustVerifyEmail"
						:status="status"
						class="max-w-xl"
					/>
				</Card>

				<Card>
					<UpdatePasswordForm class="max-w-xl" />
				</Card>

				<Card>
					<DeleteUserForm class="max-w-xl" />
				</Card>
			</div>
		</div>
	</AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

defineProps({
	mustVerifyEmail: {
		type: Boolean,
	},
	status: {
		type: String,
	},
});
</script>
