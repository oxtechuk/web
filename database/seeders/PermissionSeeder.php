<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::where('guard_name', 'employee')
            ->whereNotIn('name', [
                'manage-dashboard',
                'manage-cars',
                'manage-brands',
                'manage-car-categories',
                'manage-specifications',
                'manage-features',
                'manage-offers',
                'manage-leads',
                'manage-calculator-leads',
                'manage-contact-sources',
                'manage-newsletter',
                'manage-bookings',
                'manage-tracking',
                'manage-calculator-settings',
                'manage-tasks',
                'manage-employees',
                'manage-roles',
                'manage-reports',
                'manage-blog',
                'manage-designs',
                'manage-partners',
                'manage-testimonials',
                'manage-translations',
                'manage-settings',
                'manage-settings-integrations',
                'manage-job-applications',
                'manage-faqs',
            ])->delete();

        $permissions = [
            'manage-dashboard',
            'manage-cars',
            'manage-brands',
            'manage-car-categories',
            'manage-specifications',
            'manage-features',
            'manage-offers',
            'manage-leads',
            'manage-calculator-leads',
            'manage-contact-sources',
            'manage-newsletter',
            'manage-bookings',
            'manage-tracking',
            'manage-calculator-settings',
            'manage-tasks',
            'manage-employees',
            'manage-roles',
            'manage-reports',
            'manage-blog',
            'manage-designs',
            'manage-partners',
            'manage-testimonials',
            'manage-translations',
            'manage-settings',
            'manage-settings-integrations',
            'manage-job-applications',
            'manage-faqs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'employee']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'employee']);
        $adminRole->syncPermissions($permissions);

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'employee']);
        $employeeRole->syncPermissions([
            'manage-dashboard',
            'manage-cars',
            'manage-leads',
            'manage-bookings',
            'manage-tracking',
            'manage-tasks',
        ]);

        $hrRole = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'employee']);
        $hrRole->syncPermissions([
            'manage-dashboard',
            'manage-job-applications',
        ]);

        foreach (\App\Models\Employee::all() as $emp) {
            if ($emp->role === 'admin') {
                $emp->assignRole('admin');
            } else {
                $emp->assignRole('employee');
            }
        }
    }
}
