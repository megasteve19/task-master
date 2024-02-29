<template>
	<div class="flex -space-x-4">
		<div
			v-for="user in props.searchable ? users : props.users"
			:key="user.id"
			class="relative group"
			:class="{
				'cursor-pointer': searchable,
			}"
			@click="search.remove(user)"
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

		<template v-if="searchable">
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
						>
							<AddIcon class="w-4 h-4" />
						</button>
					</template>

					<template #content>
						<div class="px-4 mb-2">
							<InputLabel>Search</InputLabel>

							<TextInput
								class="min-w-48"
								v-model="search.query"
								placeholder="Search for a user..."
								sm
							/>
						</div>

						<DropdownButton
							v-if="search.results.length !== 0"
							v-for="user in search.results"
							:key="user.id"
							@click="search.add(user)"
						>
							<img
								:src="user.avatar"
								:alt="user.name"
								class="w-8 h-8 rounded-full shadow-sm"
							/>

							<span>{{ user.name }}</span>
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
import { reactive } from 'vue';
import { watch } from 'vue';

const props = defineProps({
	users: [Array, null],
	searchable: {
		type: Boolean,
		default: false,
	},
});

/**
 * The users that are selected.
 */
const users = defineModel({
	required: false,
	type: Array,
	default: [],
});

/**
 * Ref of the dropdown.
 */
const dropdown = ref(null);

/**
 * The search state.
 */
const search = reactive({
	/**
	 * The user query.
	 */
	query: '',

	/**
	 * The search results.
	 */
	results: [],

	/**
	 * Perform the search.
	 */
	async perform() {
		const results = await fetch(route('api.search.users', {
			query: this.query,
			except: users.value.map((user) => user.id),
			limit: 5,
		}), { headers: { 'accept': 'application/json' } });

		this.results = (await results.json()).data;
	},

	/**
	 * Add a user to the selected users.
	 */
	add(user) {
		users.value.push(user);
		this.query = '';
		this.results = [];
		dropdown.value.close();
	},

	/**
	 * Remove a user from the selected users.
	 */
	remove(user) {
		users.value = users.value.filter((u) => u.id !== user.id);
	},
});

/**
 * Watch the query and perform the search.
 */
watch(() => search.query, () => {
	if (search.query === '') {
		search.results = [];
		return;
	}

	search.perform();
});
</script>