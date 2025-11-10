<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RPAUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if RPA user already exists
        $existingUser = User::where('email', 'rpa@kulonda.ao')->first();
        
        if (!$existingUser) {
            User::create([
                'name' => 'RPA User',
                'email' => 'rpa@kulonda.ao',
                'password' => Hash::make('RPA@Kulonda2024'),
                'email_verified_at' => now(),
            ]);
            
            $this->command->info("RPA user created successfully!");
        } else {
            $this->command->info("RPA user already exists.");
        }
    }
}
