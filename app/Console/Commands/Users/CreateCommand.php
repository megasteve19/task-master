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
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateCommand extends Command
{
    use ValidatesPrompts;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            'name' => text(
                label: 'What is the name of the user?',
                placeholder: 'John Doe',
                validate: fn ($value) => $this->validate('name', $value, 'string|max:255'),
                required: true
            ),
            'email' => text(
                label: 'What is the email of the user?',
                placeholder: 'john@example.com',
                validate: fn ($value) => $this->validate('email', $value, ['email', Rule::unique(User::class, 'email')]),
                required: true
            ),
            'role' => select(
                label: 'What is the role of the user?',
                options: collect(UserRole::cases())
                    ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->prettyName()]),
                default: UserRole::User->value,
                validate: fn ($value) => $value === UserRole::Owner->value && User::whereRole(UserRole::Owner)->exists()
                    ? 'Already an owner exists. There can be only one owner.'
                    : null,
                required: true
            ),
            'password' => password(
                label: 'What is the password of the user?',
                validate: fn ($value) => $this->validate('password', $value, 'required|string|min:8'),
                required: true
            ),
        ];

        while (true)
        {
            $confirmation = password(
                label: 'Please confirm the password',
                required: true,
            );

            if ($confirmation !== $data['password'])
            {
                error('The passwords do not match. Please try again.');
                continue;
            }

            break;
        }

        if (!confirm('Do you want to proceed with the user creation?'))
        {
            error('User creation cancelled.');

            return;
        }

        User::create($data);

        info('User created successfully.');
    }
}
