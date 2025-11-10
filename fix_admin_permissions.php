<?php
require __DIR__."/vendor/autoload.php";

$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== FIXING ADMIN PERMISSIONS ===\n\n";

// Get admin user
$admin = \App\Models\User::where("user_type", "admin")->first();

if (!$admin) {
    echo "❌ No admin user found!\n";
    exit(1);
}

echo "✓ Admin user found: {$admin->name} (ID: {$admin->id})\n\n";

// Check if staff table exists
try {
    $staffExists = \Schema::hasTable("staff");
    echo "Staff table: " . ($staffExists ? "✓ EXISTS" : "✗ NOT FOUND") . "\n";
    
    $rolesExists = \Schema::hasTable("roles");
    echo "Roles table: " . ($rolesExists ? "✓ EXISTS" : "✗ NOT FOUND") . "\n\n";
    
    if (!$staffExists || !$rolesExists) {
        echo "❌ Required tables not found!\n";
        exit(1);
    }
    
    // Check for existing staff record
    $staff = \App\Models\Staff::where("user_id", $admin->id)->first();
    
    if ($staff) {
        echo "✓ Staff record already exists (ID: {$staff->id})\n";
    } else {
        echo "⚠️  Creating new staff record...\n";
        
        // Get or create a super admin role
        $role = \App\Models\Role::first();
        
        if (!$role) {
            echo "Creating default admin role...\n";
            $role = new \App\Models\Role();
            $role->name = "Administrator";
            $role->permissions = json_encode([
                "product_manage", "product_add", "product_edit", "product_delete",
                "category_manage", "brand_manage", "customer_manage", "order_manage",
                "staff_manage", "marketing", "support", "reports"
            ]);
            $role->save();
            echo "✓ Role created (ID: {$role->id})\n";
        } else {
            echo "✓ Using existing role: {$role->name} (ID: {$role->id})\n";
        }
        
        // Create staff record
        $staff = new \App\Models\Staff();
        $staff->user_id = $admin->id;
        $staff->role_id = $role->id;
        $staff->save();
        
        echo "✓ Staff record created (ID: {$staff->id})\n";
    }
    
    // Verify permissions
    echo "\n=== VERIFICATION ===\n";
    $staff = \App\Models\Staff::where("user_id", $admin->id)->first();
    
    if ($staff && $staff->role_id) {
        $role = \App\Models\Role::find($staff->role_id);
        echo "✓ Admin has staff record\n";
        echo "✓ Role: {$role->name}\n";
        echo "✓ Permissions: {$role->permissions}\n";
        echo "\n✅ ADMIN PERMISSIONS FIXED!\n";
    } else {
        echo "❌ Something went wrong!\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
