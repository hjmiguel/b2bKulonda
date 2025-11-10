<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateRPAUser extends Command
{
    protected $signature = 'user:create-rpa';
    protected $description = 'Create RPA user for the system';

    public function handle()
    {
        try {
            $existingUser = User::where('email', 'rpa@kulonda.ao')->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'name' => 'RPA User',
                    'email' => 'rpa@kulonda.ao',
                    'password' => Hash::make('RPA@Kulonda2024'),
                    'email_verified_at' => now(),
                ]);
                
                $this->info('RPA user created successfully!');
                $this->info('Email: rpa@kulonda.ao');
                $this->info('Password: RPA@Kulonda2024');
                
                return 0;
            } else {
                $this->warn('RPA user already exists.');
                $this->info('Email: ' . $existingUser->email);
                
                return 0;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
