# Task Master

## How to run the app

This project has `laravel/sail` installed, so if you have Docker installed, you can run the app without much hassle.

First, clone the repository and navigate to the project directory.

```bash
git clone git@github.com:megasteve19/task-master.git && cd task-master
```

Next, copy the `.env.sail` file to `.env`:

```bash
cp .env.sail .env
```

Then, run following command to install the composer dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

And for the NPM dependencies:

```bash
npm install
```

Take a build of the frontend:

```bash
npm run build # or npm run dev
```

Finally, run the following command to start the app:

```bash
./vendor/bin/sail up
```

Now you can head over to [http://localhost](http://localhost) to view the app.

For further instructions, please refer to the [Laravel Sail documentation](https://laravel.com/docs/10.x/sail).

## Migration

For testing purposes, you can use the following command to migrate and seed the database:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

Default credentials for the seeded user are:

- Email: `owner@example.com`
- Password: `password`

## Commands

There's few useful commands, since `users` page is not implemented in the app, instead you can use the following commands to manage users:

- `./vendor/bin/sail artisan users:list` - List all users
- `./vendor/bin/sail artisan users:create` - Create a new user
- `./vendor/bin/sail artisan users:update` - Update a user
- `./vendor/bin/sail artisan users:delete` - Delete a user

Other commands are;

- `./vendor/bin/sail artisan projects:list` - List all projects
- `./vendor/bin/sail artisan tasks:list` - List all tasks

These are already implemented in the app, but they exists as commands for testing purposes.

For development purposes, this project uses `barryvdh/laravel-ide-helper` package. You can generate the IDE helper files by running the following command:

```bash
./vendor/bin/sail composer ide-helper
```

This will genereate all possible helper files for the project.

## Features

### Projects

Projects are the main feature of the app. You can create, update, trash, delete, archive and restore projects. Projects can have multiple assigned users and if assigned user role is `user`, they can only see the project, but can't manage it.

Project has following properties:

- `name` - Name of the project
- `description` - Description of the project
- `due_date` - Due date of the project
- `archived_at` - Date when the project is archived
- `deleted_at` - Date when the project is trashed
- `assignees[]` - Belongs to many relation of `User` model

### Tasks

Tasks are the sub-feature of the app. You can create, update, trash, delete, archive and restore tasks. Tasks can have multiple assigned users, every user can manage tasks freely if they have access to the project.

Task has following properties:

- `name` - Name of the task
- `description` - Description of the task
- `due_date` - Due date of the task
- `status` - Status of the task, can be `todo`, `in_progress`, `completed`
- `priority` - Priority of the task, can be ordered in UI by sorting
- `archived_at` - Date when the task is archived
- `deleted_at` - Date when the task is trashed
- `assignees[]` - Belongs to many relation of `User` model

### Users

### Role Schema

This app has a simple role schema, which is as follows:

- `owner` - Can manage all projects and tasks, manages all users
- `admin` - Can manage all projects and tasks, manages all users except `owner` and `admin`
- `user` - Can see own projects, but can't manage them. Can see all tasks under own projects, can manage all tasks.

Role system not implemented perfectly, since there's very few pages and actions in the app, I just used `FormRequest::authorize` method to check the user's permission to do the action. Normally, I would use `Gates` and `Policies` to manage the permissions.

### Fuzzy Search

Thanks to `laravel/scout` and `typesense` packages, the app has a fuzzy search feature. You can search on projects, tasks or globally and get pretty decent results.

### What is missing?

- `users` page - I wanted to implement a page to manage users, but I couldn't make it in time, so I implemented the commands to manage users.
- `settings` page - I wanted to implement a page to manage settings, dark mode, timezone, language etc.
- `password reset` - On the login page, I wanted to implement a password reset feature.
- `invitations` - I wanted to implement a feature to invite users to the app.
- `notifications` - I wanted to implement a feature to notify users about the changes in the app.
- `activity log` - I wanted to implement a feature to log all the activities in the app.
- `comments` - I wanted to implement a feature to comment on tasks and projects.

Other than these, there's a lot of room for improvement and optimization in the app, for example I didn't implement `pagination`.
