<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user admin or create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter user email');

        $user = User::where('email', $email)->first();

        if ($user) {
            // User exists, make them admin
            $user->role = 'admin';
            $user->save();

            $this->info("✅ User '{$user->name}' ({$email}) is now an admin!");
        } else {
            // User doesn't exist, create new admin
            $this->warn("User not found. Let's create a new admin user.");

            $name = $this->ask('Enter admin name', 'Super Admin');
            $password = $this->secret('Enter password (leave empty for "password")') ?: 'password';
            $phone = $this->ask('Enter phone number (optional)', '12345678');

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'phone' => $phone,
                'locale' => 'ar',
            ]);

            $this->info("✅ New admin user created successfully!");
            $this->table(
                ['Name', 'Email', 'Password', 'Role'],
                [[$user->name, $user->email, $password, $user->role]]
            );
        }

        $this->newLine();
        $this->info('🔗 Access the admin dashboard at: http://localhost:8000/admin/dashboard');
    }
}
