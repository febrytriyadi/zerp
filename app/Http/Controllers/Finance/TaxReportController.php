<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\TaxInvoice;
use App\Models\Finance\TaxReport;
use App\Services\Finance\TaxCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxReportController extends Controller
{
    public function __construct(
        protected TaxCalculationService $taxCalculationService,
    ) {}

    public function index()
    {
        $reports = TaxReport::paginate(10);

        return view('finance.tax-reports.index', compact('reports'));
    }

    public function create()
    {
        $reportTypes = [
            'ppn_1111' => 'SPT Masa PPN 1111',
            'pph_23' => 'SPT Masa PPh 23',
            'pph_42' => 'SPT Masa PPh 4(2)',
            'pph_21' => 'SPT Masa PPh 21',
        ];

        return view('finance.tax-reports.create', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:ppn_1111,pph_23,pph_42,pph_21',
            'period_code' => 'required|max:10',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable',
        ]);

        $validated['company_id'] = $request->company_id ?? auth()->user()->company_id ?? 1;
        $validated['branch_id'] = $request->branch_id ?? auth()->user()->branch_id ?? 1;
        $validated['created_by'] = auth()->id();

        $summary = $this->calculateReportSummary(
            $validated['report_type'],
            $validated['period_start'],
            $validated['period_end']
        );

        $validated['total_dpp'] = $summary['total_dpp'];
        $validated['total_tax'] = $summary['total_tax'];
        $validated['total_withheld'] = $summary['total_withheld'];

        TaxReport::create($validated);

        return redirect()->route('finance.tax-reports.index')
            ->with('success', 'Laporan pajak berhasil dibuat.');
    }

    public function show(TaxReport $taxReport)
    {
        return view('finance.tax-reports.show', compact('taxReport'));
    }

    public function edit(TaxReport $taxReport)
    {
        $reportTypes = [
            'ppn_1111' => 'SPT Masa PPN 1111',
            'pph_23' => 'SPT Masa PPh 23',
            'pph_42' => 'SPT Masa PPh 4(2)',
            'pph_21' => 'SPT Masa PPh 21',
        ];

        return view('finance.tax-reports.edit', compact('taxReport', 'reportTypes'));
    }

    public function update(Request $request, TaxReport $taxReport)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:ppn_1111,pph_23,pph_42,pph_21',
            'period_code' => 'required|max:10',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable',
        ]);

        $summary = $this->calculateReportSummary(
            $validated['report_type'],
            $validated['period_start'],
            $validated['period_end']
        );

        $validated['total_dpp'] = $summary['total_dpp'];
        $validated['total_tax'] = $summary['total_tax'];
        $validated['total_withheld'] = $summary['total_withheld'];

        $taxReport->update($validated);

        return redirect()->route('finance.tax-reports.index')
            ->with('success', 'Laporan pajak berhasil diupdate.');
    }

    public function destroy(TaxReport $taxReport)
    {
        $taxReport->delete();

        return redirect()->route('finance.tax-reports.index')
            ->with('success', 'Laporan pajak berhasil dihapus.');
    }

    protected function calculateReportSummary(string $reportType, string $startDate, string $endDate): array
    {
        $query = TaxInvoice::whereBetween('tax_invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'draft');

        return match ($reportType) {
            'ppn_1111' => [
                'total_dpp' => (clone $query)->sum('dpp'),
                'total_tax' => (clone $query)->sum('ppn_amount'),
                'total_withheld' => 0,
            ],
            'pph_23' => [
                'total_dpp' => (clone $query)->where('transaction_type', 'purchase')->sum('dpp'),
                'total_tax' => 0,
                'total_withheld' => (clone $query)->where('transaction_type', 'purchase')->sum('ppn_amount'),
            ],
            'pph_42' => [
                'total_dpp' => (clone $query)->where('transaction_type', 'purchase')->sum('dpp'),
                'total_tax' => 0,
                'total_withheld' => (clone $query)->where('transaction_type', 'purchase')->sum('ppn_amount'),
            ],
            'pph_21' => [
                'total_dpp' => 0,
                'total_tax' => 0,
                'total_withheld' => 0,
            ],
            default => ['total_dpp' => 0, 'total_tax' => 0, 'total_withheld' => 0],
        };
    }
}
