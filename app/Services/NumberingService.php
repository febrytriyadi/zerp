<?php
namespace App\Services;

use App\Models\Master\NumberingFormat;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    public function generate(string $transactionType, int $companyId, int $branchId, string $date): string
    {
        return DB::transaction(function () use ($transactionType, $companyId, $branchId, $date) {
            $format = NumberingFormat::where('transaction_type', $transactionType)
                ->where('company_id', $companyId)
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->firstOrFail();

            $currentYear = (int) date('Y', strtotime($date));
            $currentMonth = (int) date('m', strtotime($date));

            if ($format->reset_period === 'yearly' && $format->last_year !== $currentYear) {
                $format->next_number = 1;
                $format->last_year = $currentYear;
            } elseif ($format->reset_period === 'monthly' && ($format->last_year !== $currentYear || $format->last_month !== $currentMonth)) {
                $format->next_number = 1;
                $format->last_year = $currentYear;
                $format->last_month = $currentMonth;
            }

            $number = str_pad($format->next_number, 6, '0', STR_PAD_LEFT);
            $formatted = str_replace(
                ['{PREFIX}', '{YEAR}', '{MONTH}', '{NUMBER}'],
                [$format->prefix, $currentYear, str_pad($currentMonth, 2, '0', STR_PAD_LEFT), $number],
                $format->format
            );

            $format->increment('next_number');
            $format->save();

            return $formatted;
        });
    }
}
