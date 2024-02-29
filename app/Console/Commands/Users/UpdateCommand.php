<?php

namespace App\Console\Commands\Users;

use App\Console\Commands\Concerns\ValidatesPrompts;
use App\Enums\UserRole;

use App\Models\User;
use Illuminate\Console\Command;

use Illuminate\Validation\Rule;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class UpdateCommand extends Command
{
    use ValidatesPrompts;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = search(
            label: 'Which user do you want to update?',
            options: fn (string $value) => !empty($value)
                ? User::search($value)->get()->pluck('name', 'id')->toArray()
                : User::pluck('name', 'id')->toArray(),
        );

        $user = User::find($userId);

        $whatToUpdate = select(
            label: 'What do you want to update?',
            options: [
                'name' => 'Name',
                'email' => 'Email',
                'role' => 'Role',
                'password' => 'Password',
            ],
        );

        if ($whatToUpdate === 'name')
        {
            $data = [
                'name' => text(
                    label: 'What is the new name?',
                    default: $user->name,
                    validate: fn ($value) => $this->validate('name', $value, 'string|max:255'),
                    required: true
                ),
            ];
        }
        elseif ($whatToUpdate === 'email')
        {
            $data = [
                'email' => text(
                    label: 'What is the new email?',
                    default: $user->email,
                    validate: fn ($value) => $this->validate('email', $value, ['string', 'email', Rule::unique('users', 'email')->ignore($user->id)]),
                    required: true
                ),
            ];
        }
        elseif ($whatToUpdate === 'role')
        {
            $data = [
                'role' => select(
                    label: 'What is the new role?',
                    options: collect(UserRole::cases())
                        ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->prettyName()]),
                    default: $user->role->value,
                    validate: fn ($value) => $value === UserRole::Owner->value && User::whereRole(UserRole::Owner)->where('id', '!=', $user->id)->exists()
                        ? 'Already an owner exists. There can be only one owner.'
                        : null,
                    required: true
                ),
            ];
        }
        elseif ($whatToUpdate === 'password')
        {
            $data = [
                'password' => password(
                    label: 'What is the new password?',
                    validate: fn ($value) => $this->validate('password', $value, 'required|string|min:8'),
                    required: true
                ),
            ];

            while (true)
            {
                $confirmation = password(
                    label: 'Please confirm the new password',
                    required: true,
                );

                if ($confirmation !== $data['password'])
                {
                    error('The passwords do not match. Please try again.');
                    continue;
                }

                break;
            }
        }

        if (!confirm('Are you sure you want to update this user?'))
        {
            error('User update cancelled.');

			return;
        }

		$user->update($data);

		info('User updated successfully.');
    }
}
