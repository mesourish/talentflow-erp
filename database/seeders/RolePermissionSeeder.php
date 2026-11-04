<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    Database\Seeders
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class RolePermissionSeeder
 *
 * Enterprise Seeder responsible for RolePermissionSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'celebrities.view', 'celebrities.create', 'celebrities.edit', 'celebrities.manage_rates',
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.convert',
            'projects.view', 'projects.create', 'projects.edit', 'projects.assign',
            'bookings.view', 'bookings.create', 'bookings.confirm', 'bookings.reschedule',
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            'documents.view', 'documents.upload', 'documents.download', 'documents.delete',
            'claims.view', 'claims.create', 'claims.submit', 'claims.approve', 'claims.reject',
            'finance.view', 'invoices.create', 'payments.record', 'payouts.manage',
            'reports.view', 'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $management = Role::firstOrCreate(['name' => 'Management', 'guard_name' => 'web']);
        $management->givePermissionTo([
            'clients.view', 'celebrities.view', 'quotations.view', 'quotations.approve',
            'projects.view', 'bookings.view', 'contracts.view', 'documents.view', 'documents.download',
            'claims.view', 'claims.approve', 'finance.view', 'reports.view', 'reports.export'
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'Sales Manager', 'guard_name' => 'web']);
        $salesManager->givePermissionTo([
            'clients.view', 'clients.create', 'clients.edit',
            'celebrities.view',
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.convert',
            'projects.view', 'reports.view'
        ]);

        $crmExec = Role::firstOrCreate(['name' => 'CRM Executive', 'guard_name' => 'web']);
        $crmExec->givePermissionTo([
            'clients.view', 'clients.create', 'clients.edit',
            'celebrities.view', 'quotations.view', 'quotations.create'
        ]);

        $talentManager = Role::firstOrCreate(['name' => 'Talent Manager', 'guard_name' => 'web']);
        $talentManager->givePermissionTo([
            'celebrities.view', 'celebrities.create', 'celebrities.edit', 'celebrities.manage_rates',
            'bookings.view', 'bookings.create', 'bookings.confirm', 'bookings.reschedule',
            'contracts.view', 'reports.view'
        ]);

        $projectManager = Role::firstOrCreate(['name' => 'Project Manager', 'guard_name' => 'web']);
        $projectManager->givePermissionTo([
            'projects.view', 'projects.create', 'projects.edit', 'projects.assign',
            'bookings.view', 'bookings.create', 'bookings.confirm',
            'contracts.view', 'documents.view', 'documents.upload', 'documents.download',
            'claims.view', 'claims.approve', 'reports.view'
        ]);

        $bookingCoordinator = Role::firstOrCreate(['name' => 'Booking Coordinator', 'guard_name' => 'web']);
        $bookingCoordinator->givePermissionTo([
            'celebrities.view', 'bookings.view', 'bookings.create', 'bookings.confirm', 'bookings.reschedule'
        ]);

        $documentManager = Role::firstOrCreate(['name' => 'Document Manager', 'guard_name' => 'web']);
        $documentManager->givePermissionTo([
            'contracts.view', 'contracts.create', 'contracts.edit',
            'documents.view', 'documents.upload', 'documents.download', 'documents.delete'
        ]);

        $financeManager = Role::firstOrCreate(['name' => 'Finance Manager', 'guard_name' => 'web']);
        $financeManager->givePermissionTo([
            'claims.view', 'claims.approve', 'finance.view', 'invoices.create', 'payments.record', 'payouts.manage',
            'reports.view', 'reports.export'
        ]);

        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employee->givePermissionTo([
            'projects.view', 'claims.view', 'claims.create', 'claims.submit'
        ]);
    }
}
