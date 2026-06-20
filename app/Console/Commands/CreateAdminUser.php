<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use App\Http\Services\PasswordValidationService;


#[Signature('app:create-admin-user')]
#[Description('Create a new admin securely via terminal')]
class CreateAdminUser extends Command
{
    public function handle()
    {
        $firstName = trim($this->ask('First name'));
        $lastName = trim($this->ask('Last name'));
        $username = trim($this->ask('Username'));
        $email = trim($this->ask('Email'));
        $phone_number = trim($this->ask('Phone'));
        $password = trim($this->ask('Password'));

        try {

            if (!$firstName || !$lastName || !$username || !$email || !$password) {
                $this->error("All fields are required!");
                return self::FAILURE;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error("Invalid email format!");
                return self::FAILURE;
            }


            if (User::where('username', $username)->exists()) {
                $this->error("Username already exists!");
                return self::FAILURE;
            }

            if (User::where('email', $email)->exists()) {
                $this->error("Email already exists!");
                return self::FAILURE;
            }

            PasswordValidationService::validate($password);

            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'phone_number' => $phone_number,
                'role' => 'admin',
                'is_active' => true,
                'password' => $password,
            ]);

            $this->info("Admin user created successfully with ID: " . $user->id);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error creating admin user: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
