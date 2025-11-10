<?php
require __DIR__./vendor/autoload.php;
$app = require_once __DIR__./bootstrap/app.php;
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING SPATIE PERMISSION TABLES ===\n\n";

$tables = ['permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo "✓ Table '$table' exists - $count records\n";
        
        if ($table == 'permissions') {
            $perms = DB::table($table)->select('name', 'guard_name')->limit(10)->get();
            echo "  Sample permissions:\n";
            foreach ($perms as $perm) {
                echo "    - {$perm->name} ({$perm->guard_name})\n";
            }
        }
        
        if ($table == 'roles') {
            $roles = DB::table($table)->select('id', 'name', 'guard_name')->get();
            echo "  Roles:\n";
            foreach ($roles as $role) {
                echo "    - ID:{$role->id} {$role->name} ({$role->guard_name})\n";
            }
        }
        
        if ($table == 'model_has_roles') {
            $userRoles = DB::table($table)->where('model_type', 'App\\Models\\User')->get();
            echo "  User role assignments:\n";
            foreach ($userRoles as $ur) {
                $user = DB::table('users')->where('id', $ur->model_id)->first();
                $role = DB::table('roles')->where('id', $ur->role_id)->first();
                echo "    - User #{$ur->model_id} ({$user->name}) => Role #{$ur->role_id} ({$role->name})\n";
            }
        }
        
        if ($table == 'role_has_permissions') {
            $rolePerms = DB::table($table)->get();
            echo "  Role permission assignments: {$rolePerms->count()} total\n";
            if ($rolePerms->count() > 0) {
                echo "  Sample role-permission mappings:\n";
                foreach ($rolePerms->take(5) as $rp) {
                    $perm = DB::table('permissions')->where('id', $rp->permission_id)->first();
                    echo "    - Role #{$rp->role_id} => Permission #{$rp->permission_id} ({$perm->name})\n";
                }
            }
        }
        
        echo "\n";
    } else {
        echo "✗ Table '$table' does NOT exist\n\n";
    }
}

echo "\n=== CHECKING CURRENT ADMIN USER ===\n";
$admin = DB::table('users')->where('email', 'admin@kulonda.ao')->first();
if ($admin) {
    echo "Admin user found: ID {$admin->id}, Email: {$admin->email}, User Type: {$admin->user_type}\n";
    
    // Check if using Spatie
    $adminRoles = DB::table('model_has_roles')
        ->where('model_type', 'App\\Models\\User')
        ->where('model_id', $admin->id)
        ->get();
    
    if ($adminRoles->count() > 0) {
        echo "Admin has Spatie roles:\n";
        foreach ($adminRoles as $ar) {
            $role = DB::table('roles')->where('id', $ar->role_id)->first();
            echo "  - {$role->name}\n";
            
            // Get permissions for this role
            $perms = DB::table('role_has_permissions')
                ->where('role_id', $ar->role_id)
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->select('permissions.name')
                ->get();
            
            echo "    Permissions: {$perms->count()} total\n";
            if ($perms->count() > 0) {
                echo "    Sample: " . $perms->take(5)->pluck('name')->implode(', ') . "\n";
            } else {
                echo "    ⚠ WARNING: No permissions assigned to this role!\n";
            }
        }
    } else {
        echo "⚠ WARNING: Admin has NO Spatie roles assigned!\n";
    }
} else {
    echo "Admin user NOT found\n";
}

echo "\n=== DONE ===\n";
