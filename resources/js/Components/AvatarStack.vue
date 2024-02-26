<template>
	<div class="flex -space-x-4">
		<div
			v-for="user in users"
			:key="user.id"
			class="relative group"
			:class="{
				'cursor-pointer': withEdit,
			}"
			@click="emit('remove', user)"
		>
			<img
				:src="user.avatar"
				:alt="user.name"
				class="w-8 h-8 border border-white rounded-full shadow-sm"
			/>

			<div
				class="absolute hidden w-auto px-2 py-1 mb-1 text-xs -translate-x-1/2 bg-white border rounded-lg shadow-sm animate-pop left-1/2 bottom-full group-hover:block whitespace-nowrap">
				{{ user.name }}
			</div>
		</div>

		<template v-if="withEdit">
			<div class="relative group">
				<Dropdown
					:close-on-content-click="false"
					align="left"
					ref="dropdown"
				>
					<template #trigger>
						<button
							type="button"
							class="flex items-center justify-center w-8 h-8 text-gray-400 bg-white border-white rounded-full shadow-sm hover:bg-gray-100"
							@click="emit('add')"
						>
							<AddIcon class="w-4 h-4" />
						</button>
					</template>

					<template #content>
						<div class="px-4 mb-2">
							<InputLabel>Search</InputLabel>

							<TextInput
								class="w-64"
								v-model="model"
								placeholder="Search for a user..."
								sm
							/>
						</div>

						<DropdownButton
							v-if="searchResults?.length ?? 0 !== 0"
							v-for="user in searchResults"
							:key="user.id"
							@click="add(user)"
						>
							<img
								:src="user.avatar"
								:alt="user.name"
								class="w-8 h-8 border border-white rounded-full shadow-sm"
							/>

							<span class="ml-2">{{ user.name }}</span>
						</DropdownButton>
						<p
							v-else
							class="px-4 py-2 text-xs text-center text-gray-500"
						>No results found</p>
					</template>
				</Dropdown>
			</div>
		</template>
	</div>
</template>

<script setup>
import AddIcon from '@/Components/Icons/Add.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownButton from './DropdownButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from 'vue';

const props = defineProps({
	users: Array,
	searchResults: Array,
	withEdit: Boolean,
});

const model = defineModel('search');

const emit = defineEmits(['add', 'remove']);

const dropdown = ref(null);

const add = (user) => {
	emit('add', user);
	model.value = '';
	dropdown.value.close();
};
</script>