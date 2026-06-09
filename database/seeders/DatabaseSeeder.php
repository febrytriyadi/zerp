<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\Master\Currency;
use App\Models\Master\Unit;
use App\Models\Master\ProductCategory;
use App\Models\Master\Product;
use App\Models\Master\Customer;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\PaymentTerm;
use App\Models\Master\TaxRate;
use App\Models\Master\NumberingFormat;
use App\Models\Master\CashAccount;
use App\Models\Master\BankAccount;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User
        $admin = User::create([
            'name' => 'Admin ZERP',
            'email' => 'admin@zerp.test',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $role = Role::firstOrCreate(['name' => 'super_admin']);
        $admin->assignRole($role);

        // 2. Company
        $company = Company::create([
            'code' => 'ZERP',
            'name' => 'PT ZERP Indonesia',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'phone' => '021-12345678',
            'email' => 'info@zerp.test',
            'tax_id' => '01.234.567.8-999.000',
            'is_active' => true,
        ]);

        // 3. Branch
        $branch = Branch::create([
            'company_id' => $company->id,
            'code' => 'HQ',
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        $admin->update(['company_id' => $company->id, 'branch_id' => $branch->id]);

        // 4. Currency
        $currencyIdr = Currency::create(['code' => 'IDR', 'name' => 'Rupiah', 'symbol' => 'Rp', 'is_base' => true])->id;
        Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => false]);

        // 5. Units (no company_id)
        $unitPcs = Unit::create(['code' => 'PCS', 'name' => 'Pieces'])->id;
        $unitKg = Unit::create(['code' => 'KG', 'name' => 'Kilogram'])->id;
        Unit::create(['code' => 'LBR', 'name' => 'Lembar']);
        Unit::create(['code' => 'LTR', 'name' => 'Liter']);
        Unit::create(['code' => 'BOX', 'name' => 'Box']);
        Unit::create(['code' => 'DUS', 'name' => 'Dus']);

        // 6. Product Categories (need code)
        $cat1 = ProductCategory::create(['company_id' => $company->id, 'code' => 'ELEC', 'name' => 'Elektronik'])->id;
        $cat2 = ProductCategory::create(['company_id' => $company->id, 'code' => 'ATK', 'name' => 'ATK'])->id;
        $cat3 = ProductCategory::create(['company_id' => $company->id, 'code' => 'MAT', 'name' => 'Material'])->id;

        // 7. Tax Rates (no company_id)
        $taxPpn = TaxRate::create(['name' => 'PPN 11%', 'rate' => 11, 'is_active' => true])->id;
        TaxRate::create(['name' => 'Non PPN', 'rate' => 0, 'is_active' => true]);

        // 8. Payment Terms (no company_id)
        $termCash = PaymentTerm::create(['code' => 'CASH', 'name' => 'Cash', 'days' => 0])->id;
        $termNet30 = PaymentTerm::create(['code' => 'NET30', 'name' => 'Net 30', 'days' => 30])->id;
        PaymentTerm::create(['code' => 'NET60', 'name' => 'Net 60', 'days' => 60]);

        // 9. Chart of Accounts
        $coaMap = [];
        $coas = [
            ['1-1010', 'Kas', 'asset', 'debit'],
            ['1-1020', 'Kas Bank', 'asset', 'debit'],
            ['1-1030', 'Bank', 'asset', 'debit'],
            ['1-1050', 'Piutang Usaha', 'asset', 'debit'],
            ['1-1060', 'Persediaan Barang', 'asset', 'debit'],
            ['1-1070', 'Uang Muka Supplier', 'asset', 'debit'],
            ['1-1080', 'PPN Masukan', 'asset', 'debit'],
            ['1-1090', 'Giro Diterima', 'asset', 'debit'],
            ['2-1010', 'Utang Usaha', 'liability', 'credit'],
            ['2-1020', 'PPN Keluaran', 'liability', 'credit'],
            ['2-1030', 'Utang Gaji', 'liability', 'credit'],
            ['2-1050', 'Uang Muka Penjualan', 'liability', 'credit'],
            ['2-1060', 'Giro Dikeluarkan', 'liability', 'credit'],
            ['3-1010', 'Modal Disetor', 'equity', 'credit'],
            ['3-1020', 'Laba Ditahan', 'equity', 'credit'],
            ['4-1010', 'Pendapatan Penjualan', 'income', 'credit'],
            ['4-1020', 'Pendapatan Lain', 'income', 'credit'],
            ['4-1030', 'Retur Penjualan', 'income', 'debit'],
            ['5-1010', 'Harga Pokok Penjualan', 'expense', 'debit'],
            ['5-1020', 'Biaya Pembelian', 'expense', 'debit'],
            ['5-1030', 'Retur Pembelian', 'expense', 'credit'],
            ['5-1040', 'Biaya Gaji', 'expense', 'debit'],
            ['5-1050', 'Biaya Sewa', 'expense', 'debit'],
            ['5-1060', 'Biaya Listrik & Air', 'expense', 'debit'],
            ['5-1070', 'Selisih Stok', 'expense', 'debit'],
        ];
        foreach ($coas as [$code, $name, $type, $normal]) {
            $coa = ChartOfAccount::create([
                'company_id' => $company->id,
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'normal_balance' => $normal,
                'is_active' => true,
            ]);
            $coaMap[$code] = $coa->id;
        }

        // 10. Warehouses
        $wh1 = Warehouse::create(['company_id' => $company->id, 'branch_id' => $branch->id, 'code' => 'GDG-01', 'name' => 'Gudang Utama'])->id;
        $wh2 = Warehouse::create(['company_id' => $company->id, 'branch_id' => $branch->id, 'code' => 'GDG-02', 'name' => 'Gudang Cadangan'])->id;

        // 11. Products
        $products = [
            ['Monitor 24 Inch', 2500000, 1500000, $cat1, $unitPcs],
            ['Keyboard Mechanic', 750000, 500000, $cat1, $unitPcs],
            ['Mouse Wireless', 250000, 150000, $cat1, $unitPcs],
            ['Kertas A4 70gr', 55000, 45000, $cat2, $unitKg],
            ['Buku Tulis Sidu', 5000, 3500, $cat2, $unitPcs],
            ['Pipa PVC 3/4', 15000, 10000, $cat3, $unitKg],
            ['Semen 50kg', 70000, 55000, $cat3, $unitKg],
            ['Printer Epson L3210', 3500000, 2800000, $cat1, $unitPcs],
        ];
        $num = 0;
        foreach ($products as [$name, $price, $cost, $catId, $unitId]) {
            $num++;
            Product::create([
                'company_id' => $company->id,
                'category_id' => $catId,
                'code' => 'BRG-' . str_pad($num, 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'selling_price' => $price,
                'purchase_price' => $cost,
                'unit_id' => $unitId,
                'cost_method' => 'average',
                'is_active' => true,
            ]);
        }

        // 12. Customers (need branch_id, payment_term_id, chart_of_account_id)
        foreach ([
            ['CUST-001', 'PT Maju Jaya', '08123456789', 'maju@email.test'],
            ['CUST-002', 'CV Sukses Abadi', '08123456788', 'sukses@email.test'],
            ['CUST-003', 'Toko Berkah', '08123456787', 'berkah@email.test'],
        ] as [$code, $name, $phone, $email]) {
            Customer::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'code' => $code,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'payment_term_id' => $termNet30,
                'chart_of_account_id' => $coaMap['1-1050'],
                'is_active' => true,
            ]);
        }

        // 13. Suppliers (need branch_id, payment_term_id, chart_of_account_id)
        foreach ([
            ['SUPP-001', 'PT Supplier Utama', '08765432100', 'supplier@email.test'],
            ['SUPP-002', 'CV Bahan Bangunan', '08765432101', 'bahan@email.test'],
        ] as [$code, $name, $phone, $email]) {
            Supplier::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'code' => $code,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'payment_term_id' => $termNet30,
                'chart_of_account_id' => $coaMap['2-1010'],
                'is_active' => true,
            ]);
        }

        // 14. Fiscal Period
        FiscalPeriod::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'code' => '2026-06',
            'name' => 'Periode Juni 2026',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'is_open' => true,
        ]);
        FiscalPeriod::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'code' => '2026-07',
            'name' => 'Periode Juli 2026',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
            'is_open' => true,
        ]);

        // 15. Numbering Formats
        $formats = [
            ['sales_quotation', 'SQ', 'SQ-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['sales_order', 'SO', 'SO-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['sales_invoice', 'INV', 'INV-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['purchase_request', 'PR', 'PR-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['purchase_order', 'PO', 'PO-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['purchase_invoice', 'PI', 'PI-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['journal_entry', 'JR', 'JR-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['cash_receipt', 'CR', 'CR-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['cash_disbursement', 'CD', 'CD-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['stock_adjustment', 'SA', 'SA-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['fixed_asset', 'AST', 'AST-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
            ['assembly', 'ASM', 'ASM-{YEAR}{MONTH}-{NUMBER}', 'monthly'],
        ];
        foreach ($formats as [$type, $prefix, $fmt, $reset]) {
            NumberingFormat::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'transaction_type' => $type,
                'format' => $fmt,
                'prefix' => $prefix,
                'next_number' => 1,
                'reset_period' => $reset,
                'last_year' => 2026,
                'last_month' => 6,
            ]);
        }

        // 16. Cash & Bank Accounts
        CashAccount::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'code' => 'KS-001',
            'name' => 'Kas Kecil',
            'chart_of_account_id' => $coaMap['1-1010'],
        ]);
        CashAccount::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'code' => 'KS-002',
            'name' => 'Kas Besar',
            'chart_of_account_id' => $coaMap['1-1010'],
        ]);
        BankAccount::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'code' => 'BCA-001',
            'bank_name' => 'BCA',
            'account_name' => 'PT ZERP Indonesia',
            'account_number' => '1234567890',
            'currency_id' => $currencyIdr,
            'chart_of_account_id' => $coaMap['1-1030'],
            'opening_balance' => 200000000,
        ]);

        $this->command->info('✅ Seed data berhasil!');
    }
}
