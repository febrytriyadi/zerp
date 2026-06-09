<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'company', 'branch', 'fiscal-period', 'chart-of-account',
            'cash-account', 'bank-account', 'customer', 'supplier',
            'product', 'warehouse', 'currency',
            'cash-receipt', 'cash-disbursement', 'bank-receipt', 'bank-disbursement',
            'giro', 'bank-reconciliation', 'journal-entry', 'recurring-transaction',
            'sales-quotation', 'sales-order', 'sales-down-payment',
            'delivery-order', 'sales-invoice', 'sales-return', 'customer-payment',
            'purchase-request', 'purchase-order', 'purchase-down-payment',
            'received-goods', 'purchase-invoice', 'purchase-return', 'supplier-payment',
            'transfer', 'stock-opname', 'stock-adjustment',
            'assembly', 'inventory-movement',
            'fixed-asset',
            'tax-invoice', 'tax-report',
            'closing-journal', 'accrual',
            'report', 'dashboard', 'user', 'role',
        ];

        $actions = ['create', 'read', 'update', 'delete', 'submit', 'approve', 'reject', 'post', 'void', 'export', 'print'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $financeManager = Role::firstOrCreate(['name' => 'Finance Manager', 'guard_name' => 'web']);
        $financeManager->givePermissionTo(Permission::whereIn('name', [
            'cash-receipt.create','cash-receipt.read','cash-receipt.update','cash-receipt.delete',
            'cash-receipt.submit','cash-receipt.approve','cash-receipt.reject','cash-receipt.post','cash-receipt.void',
            'cash-disbursement.create','cash-disbursement.read','cash-disbursement.update','cash-disbursement.delete',
            'cash-disbursement.submit','cash-disbursement.approve','cash-disbursement.reject','cash-disbursement.post','cash-disbursement.void',
            'bank-receipt.read','bank-receipt.post','bank-disbursement.read','bank-disbursement.post',
            'giro.read','giro.create','giro.post','giro.approve',
            'bank-reconciliation.create','bank-reconciliation.read','bank-reconciliation.post',
            'journal-entry.create','journal-entry.read','journal-entry.post','journal-entry.void',
            'recurring-transaction.create','recurring-transaction.read',
            'customer.read','supplier.read',
            'report.read','dashboard.read',
            'chart-of-account.read','fiscal-period.read',
            'fixed-asset.create','fixed-asset.read','fixed-asset.update','fixed-asset.delete',
            'fixed-asset.post','fixed-asset.void',
            'tax-invoice.create','tax-invoice.read','tax-invoice.update','tax-invoice.delete',
            'tax-report.create','tax-report.read','tax-report.update',
        ])->pluck('name'));

        $accountingStaff = Role::firstOrCreate(['name' => 'Accounting Staff', 'guard_name' => 'web']);
        $accountingStaff->givePermissionTo(Permission::whereIn('name', [
            'journal-entry.create','journal-entry.read','journal-entry.update','journal-entry.post','journal-entry.void',
            'cash-receipt.read','cash-disbursement.read','bank-receipt.read','bank-disbursement.read',
            'chart-of-account.read',
            'report.read','dashboard.read',
        ])->pluck('name'));

        $salesStaff = Role::firstOrCreate(['name' => 'Sales Staff', 'guard_name' => 'web']);
        $salesStaff->givePermissionTo(Permission::whereIn('name', [
            'sales-quotation.create','sales-quotation.read','sales-quotation.update','sales-quotation.delete','sales-quotation.submit',
            'sales-order.create','sales-order.read','sales-order.update','sales-order.delete','sales-order.submit',
            'sales-down-payment.create','sales-down-payment.read',
            'delivery-order.create','delivery-order.read','delivery-order.update',
            'sales-invoice.create','sales-invoice.read','sales-invoice.submit',
            'sales-return.create','sales-return.read',
            'customer-payment.create','customer-payment.read',
            'customer.read','product.read',
            'report.read','dashboard.read',
        ])->pluck('name'));

        $purchasingStaff = Role::firstOrCreate(['name' => 'Purchasing Staff', 'guard_name' => 'web']);
        $purchasingStaff->givePermissionTo(Permission::whereIn('name', [
            'purchase-request.create','purchase-request.read','purchase-request.update','purchase-request.delete','purchase-request.submit',
            'purchase-order.create','purchase-order.read','purchase-order.update','purchase-order.delete','purchase-order.submit',
            'purchase-down-payment.create','purchase-down-payment.read',
            'received-goods.create','received-goods.read','received-goods.update',
            'purchase-invoice.create','purchase-invoice.read','purchase-invoice.submit',
            'purchase-return.create','purchase-return.read',
            'supplier-payment.create','supplier-payment.read',
            'supplier.read','product.read',
            'report.read','dashboard.read',
        ])->pluck('name'));

        $warehouseStaff = Role::firstOrCreate(['name' => 'Warehouse Staff', 'guard_name' => 'web']);
        $warehouseStaff->givePermissionTo(Permission::whereIn('name', [
            'transfer.create','transfer.read','transfer.update','transfer.submit',
            'stock-opname.create','stock-opname.read','stock-opname.update','stock-opname.submit','stock-opname.process',
            'stock-adjustment.read',
            'inventory-movement.read',
            'product.read','warehouse.read',
            'report.read','dashboard.read',
        ])->pluck('name'));

        $productionStaff = Role::firstOrCreate(['name' => 'Production Staff', 'guard_name' => 'web']);
        $productionStaff->givePermissionTo(Permission::whereIn('name', [
            'assembly.create','assembly.read','assembly.update','assembly.submit','assembly.post',
            'inventory-movement.read',
            'product.read','warehouse.read',
            'report.read','dashboard.read',
        ])->pluck('name'));

        $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);
        $auditor->givePermissionTo(Permission::whereIn('name', [
            'cash-receipt.read','cash-disbursement.read','bank-receipt.read','bank-disbursement.read',
            'giro.read','bank-reconciliation.read',
            'journal-entry.read',
            'sales-quotation.read','sales-order.read','sales-invoice.read','customer-payment.read','sales-return.read',
            'purchase-request.read','purchase-order.read','purchase-invoice.read','supplier-payment.read','purchase-return.read',
            'inventory-movement.read','stock-opname.read','stock-adjustment.read','assembly.read',
            'report.read','dashboard.read',
            'customer.read','supplier.read','product.read','chart-of-account.read',
        ])->pluck('name'));

        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->givePermissionTo(Permission::whereIn('name', [
            'dashboard.read',
            'report.read',
        ])->pluck('name'));

        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@zerp.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('zerp123'),
                'company_id' => 1,
                'branch_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('Super Admin');
    }
}
