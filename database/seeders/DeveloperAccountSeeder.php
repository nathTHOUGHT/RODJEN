<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeveloperAccountSeeder extends Seeder
{
    public function run(): void
    {
        $email = mb_strtolower(trim((string) env('DEVELOPER_EMAIL')));
        $password = (string) env('DEVELOPER_PASSWORD');
        $name = trim((string) env('DEVELOPER_NAME')) ?: 'Developer';

        if ($email === '' || $password === '') {
            return;
        }

        Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(
                User::query()->where('email', $email)->value('id'),
            )],
            'password' => ['required', 'string', 'min:12'],
        ])->validate();

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'user_type' => UserType::Developer->value,
                'email_verified_at' => now(),
            ]);

            return;
        }

        $user->forceFill([
            'name' => $name,
            'user_type' => UserType::Developer->value,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
    }
}
