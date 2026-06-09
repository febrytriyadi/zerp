<?php
namespace App\Services\Finance;

use App\Models\Finance\FixedAsset;
use App\Services\Accounting\JournalService;
use Illuminate\Support\Facades\DB;

class AssetJournalService
{
    public function __construct(
        protected JournalService $journalService,
    ) {}

    public function postAcquisition(FixedAsset $asset): void
    {
        $desc = "Perolehan {$asset->asset_name} - {$asset->asset_number}";

        $lines = [
            new \App\Data\JournalLineData(
                chartOfAccountId: $asset->chart_of_account_id,
                debit: $asset->purchase_cost,
                credit: 0,
                description: $desc,
            ),
            new \App\Data\JournalLineData(
                chartOfAccountId: $asset->chart_of_account_id,
                debit: 0,
                credit: $asset->purchase_cost,
                description: $desc,
            ),
        ];
    }
}
