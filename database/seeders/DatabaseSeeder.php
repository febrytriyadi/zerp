<?php

namespace Database\Seeders;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\FiscalPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'DEFAULT')->first();
        if (!$company) {
            $company = Company::create([
                'code' => 'DEFAULT',
                'name' => 'Default Company',
                'address' => '-',
                'phone' => '-',
                'email' => '-',
                'tax_id' => '-',
                'is_active' => true,
            ]);
        }

        $branch = Branch::firstOrCreate(
            ['code' => 'HQ', 'company_id' => $company->id],
            [
                'name' => 'Head Office',
                'address' => '-',
                'phone' => '-',
                'is_active' => true,
            ]
        );

        FiscalPeriod::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'FY' . date('Y')],
            [
                'branch_id' => $branch->id,
                'name' => 'Fiscal Year ' . date('Y'),
                'start_date' => Carbon::create(date('Y'), 1, 1),
                'end_date' => Carbon::create(date('Y'), 12, 31),
                'is_open' => true,
                'is_closed' => false,
            ]
        );

        $this->call([
            ChartOfAccountSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $superAdmin = User::where('email', 'superadmin@zerp.local')->first();
        if ($superAdmin) {
            $superAdmin->update([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
            ]);
        }
    }
}
