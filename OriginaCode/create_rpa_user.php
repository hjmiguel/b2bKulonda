<?php

require __DIR__./vendor/autoload.php;

$app = require_once __DIR__./bootstrap/app.php;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Check if RPA user already exists
    $existingUser = User::where('email', 'rpa@kulonda.ao')->first();
    
    if (!$existingUser) {
        $user = User::create([
            'name' => 'RPA User',
            'email' => 'rpa@kulonda.ao',
            'password' => Hash::make('RPA@Kulonda2024'),
            'email_verified_at' => now(),
        ]);
        
        echo "RPA user created successfully!\n";
        echo "Email: rpa@kulonda.ao\n";
        echo "Password: RPA@Kulonda2024\n";
    } else {
        echo "RPA user already exists.\n";
        echo "Email: " . $existingUser->email . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
