<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Str::password(16, true, true, true, false);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'test@example.com',
            'password' => Hash::make($password),
        ]);

        // seed spatie roles & permissions and give the first user an admin role
        $this->call(RolesAndPermissionsSeeder::class);

        // tradeskill container templates and items
        $this->call(TradeskillContainerTemplatesSeeder::class);

        try {
            $user->assignRole('admin');
        } catch (\Throwable $e) {
        }

        $this->command->info('----------------------------------');
        $this->command->info('Your admin credentials');
        $this->command->info('----------------------------------');
        $this->command->info('Email: ' . $user->email);
        $this->command->info('Password: ' . $password);
    }
}
