<?php
require __DIR__."/vendor/autoload.php";

$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== UPDATING SUPER ADMIN PERMISSIONS ===\n\n";

// Get Super Admin role
$role = \App\Models\Role::where("name", "Super Admin")->first();

if (!$role) {
    echo "❌ Super Admin role not found!\n";
    exit(1);
}

echo "✓ Role found: {$role->name} (ID: {$role->id})\n";
echo "Current permissions: " . ($role->permissions ?: "(empty)") . "\n\n";

// Define all available permissions (comprehensive list)
$allPermissions = [
    // Products
    "1",  // View products
    "2",  // Add products
    "3",  // Edit products
    "4",  // Delete products
    
    // Categories
    "5",  // View categories
    "6",  // Add categories
    "7",  // Edit categories
    "8",  // Delete categories
    
    // Brands
    "9",   // View brands
    "10",  // Add brands
    "11",  // Edit brands
    "12",  // Delete brands
    
    // Orders
    "13",  // View orders
    "14",  // Edit orders
    "15",  // Delete orders
    
    // Customers
    "16",  // View customers
    "17",  // Edit customers
    "18",  // Ban customers
    
    // Sellers
    "19",  // View sellers
    "20",  // Edit sellers
    "21",  // Verify sellers
    "22",  // Ban sellers
    
    // Staff & Roles
    "23",  // View staff
    "24",  // Add staff
    "25",  // Edit staff
    "26",  // Delete staff
    
    // Marketing
    "27",  // Coupons
    "28",  // Flash deals
    "29",  // Newsletters
    
    // Support
    "30",  // Support tickets
    "31",  // Product queries
    "32",  // Product reviews
    
    // Reports
    "33",  // Sales report
    "34",  // Wishlist report
    "35",  // User search report
    "36",  // Commission report
    "37",  // Wallet report
    
    // Other
    "38",  // Attribute manage
    "39",  // Shipping manage
    "40",  // General settings
    "41",  // Payments
    "42",  // Languages
    "43",  // System updates
    "44",  // Addons
];

// Update role permissions
$role->permissions = json_encode($allPermissions);
$role->save();

echo "✅ PERMISSIONS UPDATED!\n\n";
echo "New permissions: {$role->permissions}\n";
echo "\nTotal permissions granted: " . count($allPermissions) . "\n";

echo "\n=== VERIFICATION ===\n";
$admin = \App\Models\User::where("user_type", "admin")->first();
$staff = \App\Models\Staff::where("user_id", $admin->id)->first();

if ($staff && $staff->role_id == $role->id) {
    echo "✓ Admin user: {$admin->name}\n";
    echo "✓ Staff ID: {$staff->id}\n";
    echo "✓ Role: {$role->name}\n";
    echo "✓ Permissions: " . count(json_decode($role->permissions)) . " granted\n";
    echo "\n✅ ADMIN NOW HAS FULL ACCESS!\n";
} else {
    echo "⚠️  Warning: Admin staff record might not be linked properly\n";
}
