<?php
require __DIR__."/vendor/autoload.php";

$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== CHECKING ADMIN USERS AND PERMISSIONS ===\n\n";

// Get all admin users
$admins = \App\Models\User::where("user_type", "admin")->get();

echo "Total admin users: " . $admins->count() . "\n\n";

foreach ($admins as $admin) {
    echo "-----------------------------------\n";
    echo "ID: " . $admin->id . "\n";
    echo "Name: " . $admin->name . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "User Type: " . $admin->user_type . "\n";
    echo "Banned: " . ($admin->banned ?? "0") . "\n";
    
    // Check if Staff model exists and get permissions
    if (class_exists("\App\Models\Staff")) {
        $staff = \App\Models\Staff::where("user_id", $admin->id)->first();
        if ($staff) {
            echo "Staff ID: " . $staff->id . "\n";
            echo "Role ID: " . ($staff->role_id ?? "N/A") . "\n";
            
            // Get role details
            if ($staff->role_id && class_exists("\App\Models\Role")) {
                $role = \App\Models\Role::find($staff->role_id);
                if ($role) {
                    echo "Role Name: " . $role->name . "\n";
                    echo "Permissions: " . $role->permissions . "\n";
                }
            }
        } else {
            echo "⚠️  WARNING: No Staff record found!\n";
        }
    }
    echo "\n";
}

// Check tables structure
echo "\n=== CHECKING TABLES ===\n";
$tables = ["users", "staff", "roles"];
foreach ($tables as $table) {
    $exists = \DB::select("SHOW TABLES LIKE ?", [$table]);
    echo $table . ": " . (count($exists) > 0 ? "✓ EXISTS" : "✗ NOT FOUND") . "\n";
}

echo "\n=== DONE ===\n";
