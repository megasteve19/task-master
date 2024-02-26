import { usePage } from '@inertiajs/vue3';

export function isRole(role) {
	const userRole = usePage().props.auth.user.role;

	if (role === 'owner') {
		return userRole === 'owner';
	}

	if (role === 'user') {
		return userRole === 'user';
	}

	if (role === 'admin') {
		return ['owner', 'admin'].includes(userRole);
	}

	return false;
}
