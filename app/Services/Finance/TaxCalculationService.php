<?php
namespace App\Services\Finance;

use App\Models\Master\TaxRate;

class TaxCalculationService
{
    public function calculatePpn(float $dpp, ?int $taxRateId = null): array
    {
        if ($taxRateId) {
            $taxRate = TaxRate::find($taxRateId);
            $rate = $taxRate?->rate ?? 11;
        } else {
            $rate = 11;
        }

        $ppn = round($dpp * $rate / 100, 2);

        return [
            'dpp' => $dpp,
            'rate' => $rate,
            'ppn' => $ppn,
            'total' => $dpp + $ppn,
        ];
    }

    public function calculatePph23(float $amount, float $rate = 2): array
    {
        $pph23 = round($amount * $rate / 100, 2);

        return [
            'gross_amount' => $amount,
            'rate' => $rate,
            'pph23' => $pph23,
            'net_amount' => $amount - $pph23,
        ];
    }

    public function calculatePph4Ayat2(float $amount, float $rate = 10): array
    {
        $pph42 = round($amount * $rate / 100, 2);

        return [
            'gross_amount' => $amount,
            'rate' => $rate,
            'pph42' => $pph42,
            'net_amount' => $amount - $pph42,
        ];
    }

    public function calculatePph21(float $grossIncome, array $deductions = []): array
    {
        $deductionTotal = array_sum($deductions);
        $netIncome = $grossIncome - $deductionTotal;

        $tax = 0;
        $remaining = $netIncome;
        $brackets = [
            ['limit' => 60000000, 'rate' => 5],
            ['limit' => 250000000, 'rate' => 15],
            ['limit' => 500000000, 'rate' => 25],
            ['limit' => PHP_FLOAT_MAX, 'rate' => 30],
        ];

        foreach ($brackets as $bracket) {
            if ($remaining <= 0) break;
            $taxable = min($remaining, $bracket['limit']);
            $tax += round($taxable * $bracket['rate'] / 100, 2);
            $remaining -= $bracket['limit'];
        }

        return [
            'gross_income' => $grossIncome,
            'deductions' => $deductionTotal,
            'net_income' => $netIncome,
            'pph21' => $tax,
        ];
    }
}
