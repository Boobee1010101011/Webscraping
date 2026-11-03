<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    protected $signature = 'user:create {email : Login email} {--name= : Display name}';

    protected $description = 'Create a dashboard account (the password is asked for, never passed on the command line)';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email')));
        $name = $this->option('name') ?: $this->ask('Name');
        $password = $this->secret('Password (min 8 characters)');

        if ($password !== $this->secret('Repeat password')) {
            $this->error('The passwords do not match.');

            return self::FAILURE;
        }

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        $this->info("Created {$email}.");

        return self::SUCCESS;
    }
}
