<?php

namespace Database\Seeders;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create default company
        $company = \App\Models\Master\Company::firstOrCreate(
            ['code' => 'DEFAULT'],
            ['name' => 'Default Company', 'address' => '-', 'phone' => '-', 'email' => '-', 'tax_id' => '-', 'logo' => '-', 'is_active' => true]
        );
        $companyId = $company->id;

        $accounts = [
            // ===== ASET LANCAR (1-1000) =====
            [
                'company_id' => $companyId, 'code' => '1-1000', 'name' => 'Aset Lancar',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '1-1010', 'name' => 'Kas Kecil',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1020', 'name' => 'Kas Besar',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1030', 'name' => 'Bank BCA',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1040', 'name' => 'Bank Mandiri',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1050', 'name' => 'Piutang Usaha',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1060', 'name' => 'Persediaan',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1070', 'name' => 'Uang Muka Pemasok',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1080', 'name' => 'PPN Masukan',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-1090', 'name' => 'Giro Diterima',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-1000',
            ],
            // ===== ASET TETAP (1-2000) =====
            [
                'company_id' => $companyId, 'code' => '1-2000', 'name' => 'Aset Tetap',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '1-2010', 'name' => 'Tanah',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2020', 'name' => 'Bangunan',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2021', 'name' => 'Akumulasi Penyusutan Bangunan',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2030', 'name' => 'Kendaraan',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2031', 'name' => 'Akumulasi Penyusutan Kendaraan',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2040', 'name' => 'Peralatan',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2041', 'name' => 'Akumulasi Penyusutan Peralatan',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2050', 'name' => 'Komputer',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2051', 'name' => 'Akumulasi Penyusutan Komputer',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2060', 'name' => 'Furniture & Inventaris',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2061', 'name' => 'Akumulasi Penyusutan Furniture',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2070', 'name' => 'Mesin',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2071', 'name' => 'Akumulasi Penyusutan Mesin',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2080', 'name' => 'Aset Lainnya',
                'type' => 'asset', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '1-2081', 'name' => 'Akumulasi Penyusutan Aset Lainnya',
                'type' => 'asset', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '1-2000',
            ],
            // ===== KEWAJIBAN LANCAR (2-1000) =====
            [
                'company_id' => $companyId, 'code' => '2-1000', 'name' => 'Kewajiban Lancar',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '2-1010', 'name' => 'Utang Usaha',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '2-1020', 'name' => 'PPN Keluaran',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '2-1030', 'name' => 'Utang PPh 23',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '2-1040', 'name' => 'Utang PPh 21',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '2-1050', 'name' => 'Uang Muka Pelanggan',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '2-1060', 'name' => 'Giro Dikeluarkan',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-1000',
            ],
            // ===== KEWAJIBAN JANGKA PANJANG (2-2000) =====
            [
                'company_id' => $companyId, 'code' => '2-2000', 'name' => 'Kewajiban Jangka Panjang',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '2-2010', 'name' => 'Utang Bank',
                'type' => 'liability', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '2-2000',
            ],
            // ===== MODAL (3-1000) =====
            [
                'company_id' => $companyId, 'code' => '3-1000', 'name' => 'Modal',
                'type' => 'equity', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '3-1010', 'name' => 'Modal Disetor',
                'type' => 'equity', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '3-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '3-1020', 'name' => 'Laba Ditahan',
                'type' => 'equity', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '3-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '3-1030', 'name' => 'Laba Tahun Berjalan',
                'type' => 'equity', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '3-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '3-1040', 'name' => 'Prive',
                'type' => 'equity', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '3-1000',
            ],
            // ===== PENDAPATAN (4-1000) =====
            [
                'company_id' => $companyId, 'code' => '4-1000', 'name' => 'Pendapatan',
                'type' => 'revenue', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '4-1010', 'name' => 'Penjualan Barang',
                'type' => 'revenue', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '4-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '4-1020', 'name' => 'Penjualan Jasa',
                'type' => 'revenue', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '4-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '4-1030', 'name' => 'Retur Penjualan',
                'type' => 'revenue', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '4-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '4-1040', 'name' => 'Diskon Penjualan',
                'type' => 'revenue', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '4-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '4-1050', 'name' => 'Pendapatan Lain-lain',
                'type' => 'revenue', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '4-1000',
            ],
            // ===== BEBAN POKOK PENDAPATAN (5-1000) =====
            [
                'company_id' => $companyId, 'code' => '5-1000', 'name' => 'Beban Pokok Pendapatan',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '5-1010', 'name' => 'HPP / COGS',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-1020', 'name' => 'Pembelian',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-1030', 'name' => 'Retur Pembelian',
                'type' => 'expense', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-1000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-1040', 'name' => 'Diskon Pembelian',
                'type' => 'expense', 'normal_balance' => 'kredit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-1000',
            ],
            // ===== BEBAN OPERASIONAL (5-2000) =====
            [
                'company_id' => $companyId, 'code' => '5-2000', 'name' => 'Beban Operasional',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '5-2010', 'name' => 'Beban Gaji',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2020', 'name' => 'Beban Sewa',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2030', 'name' => 'Beban Listrik & Air',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2040', 'name' => 'Beban Telepon',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2050', 'name' => 'Beban Transportasi',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2060', 'name' => 'Beban ATK',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2070', 'name' => 'Beban Penyusutan',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2080', 'name' => 'Beban Pemasaran',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2090', 'name' => 'Beban Asuransi',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-2100', 'name' => 'Beban Pemeliharaan',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-2000',
            ],
            // ===== BEBAN NON OPERASIONAL (5-3000) =====
            [
                'company_id' => $companyId, 'code' => '5-3000', 'name' => 'Beban Non Operasional',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'parent_id' => null,
            ],
            [
                'company_id' => $companyId, 'code' => '5-3010', 'name' => 'Beban Bunga',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-3000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-3020', 'name' => 'Beban Administrasi Bank',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-3000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-3030', 'name' => 'Selisih Kurs',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-3000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-3040', 'name' => 'Beban Selisih Stok',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-3000',
            ],
            [
                'company_id' => $companyId, 'code' => '5-3050', 'name' => 'Beban Lain-lain',
                'type' => 'expense', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 2, 'parent_code' => '5-3000',
            ],
        ];

        foreach ($accounts as $account) {
            $parentId = null;

            if (isset($account['parent_code'])) {
                $parent = ChartOfAccount::where('company_id', $companyId)
                    ->where('code', $account['parent_code'])
                    ->first();
                $parentId = $parent?->id;
                unset($account['parent_code']);
            }

            $account['parent_id'] = $parentId;

            ChartOfAccount::firstOrCreate(
                ['company_id' => $account['company_id'], 'code' => $account['code']],
                $account
            );
        }
    }
}
