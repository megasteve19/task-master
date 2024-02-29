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

Then, run following command to install the dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
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

## How to use the app

If you need help about how to use the app, please refer to [this video](https://youtu.be/).
