<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\FiscalPeriodController;
use App\Http\Controllers\Master\ChartOfAccountController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\WarehouseController;
use App\Http\Controllers\Finance\CashReceiptController;
use App\Http\Controllers\Finance\AssetController;
use App\Http\Controllers\Finance\CashDisbursementController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Sales\SalesQuotationController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\SalesInvoiceController;
use App\Http\Controllers\Sales\CustomerPaymentController;
use App\Http\Controllers\Sales\SalesReturnController;
use App\Http\Controllers\Purchasing\PurchaseRequestController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\PurchaseInvoiceController;
use App\Http\Controllers\Purchasing\SupplierPaymentController;
use App\Http\Controllers\Purchasing\ReceivedGoodsController;
use App\Http\Controllers\Inventory\StockOpnameController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\InventoryMovementController;
use App\Http\Controllers\Production\AssemblyController;
use App\Http\Controllers\Report\CashBookController;
use App\Http\Controllers\Report\GeneralLedgerController;
use App\Http\Controllers\Report\TrialBalanceController;
use App\Http\Controllers\Report\BalanceSheetController;
use App\Http\Controllers\Report\IncomeStatementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::name('master.')->group(function () {
        Route::get('master/companies/export-excel', [CompanyController::class, 'exportExcel'])->name('companies.export-excel');
        Route::resource('master/companies', CompanyController::class);
        Route::resource('master/fiscal-periods', FiscalPeriodController::class);
        Route::post('master/fiscal-periods/{fiscalPeriod}/close', [FiscalPeriodController::class, 'close'])->name('fiscal-periods.close');
        Route::post('master/fiscal-periods/{fiscalPeriod}/open', [FiscalPeriodController::class, 'open'])->name('fiscal-periods.open');
        Route::resource('master/chart-of-accounts', ChartOfAccountController::class);
        Route::get('master/customers/export-excel', [CustomerController::class, 'exportExcel'])->name('customers.export-excel');
        Route::resource('master/customers', CustomerController::class);
        Route::get('master/suppliers/export-excel', [SupplierController::class, 'exportExcel'])->name('suppliers.export-excel');
        Route::resource('master/suppliers', SupplierController::class);
        Route::get('master/products/export-excel', [ProductController::class, 'exportExcel'])->name('products.export-excel');
        Route::resource('master/products', ProductController::class);
        Route::resource('master/warehouses', WarehouseController::class);
    });

    // Finance
    Route::name('finance.')->group(function () {
        Route::resource('finance/cash-receipts', CashReceiptController::class);
        Route::post('finance/cash-receipts/{cashReceipt}/submit', [CashReceiptController::class, 'submit'])->name('cash-receipts.submit');
        Route::post('finance/cash-receipts/{cashReceipt}/approve', [CashReceiptController::class, 'approve'])->name('cash-receipts.approve');
        Route::post('finance/cash-receipts/{cashReceipt}/reject', [CashReceiptController::class, 'reject'])->name('cash-receipts.reject');
        Route::post('finance/cash-receipts/{cashReceipt}/post', [CashReceiptController::class, 'post'])->name('cash-receipts.post');
        Route::post('finance/cash-receipts/{cashReceipt}/void', [CashReceiptController::class, 'void'])->name('cash-receipts.void');
        Route::post('finance/cash-receipts/{cashReceipt}/cancel', [CashReceiptController::class, 'cancel'])->name('cash-receipts.cancel');

        Route::resource('finance/cash-disbursements', CashDisbursementController::class);
        Route::post('finance/cash-disbursements/{cashDisbursement}/submit', [CashDisbursementController::class, 'submit'])->name('cash-disbursements.submit');
        Route::post('finance/cash-disbursements/{cashDisbursement}/approve', [CashDisbursementController::class, 'approve'])->name('cash-disbursements.approve');
        Route::post('finance/cash-disbursements/{cashDisbursement}/reject', [CashDisbursementController::class, 'reject'])->name('cash-disbursements.reject');
        Route::post('finance/cash-disbursements/{cashDisbursement}/post', [CashDisbursementController::class, 'post'])->name('cash-disbursements.post');
        Route::post('finance/cash-disbursements/{cashDisbursement}/void', [CashDisbursementController::class, 'void'])->name('cash-disbursements.void');

        Route::resource('finance/assets', AssetController::class);
        Route::post('finance/assets/{asset}/calculate-depreciation', [AssetController::class, 'calculateDepreciation'])->name('assets.calculate-depreciation');
        Route::post('finance/assets/{asset}/sell', [AssetController::class, 'sell'])->name('assets.sell');
        Route::post('finance/assets/{asset}/retire', [AssetController::class, 'retire'])->name('assets.retire');
        Route::post('finance/assets/{asset}/revalue', [AssetController::class, 'revalue'])->name('assets.revalue');
    });

    // Accounting
    Route::name('accounting.')->group(function () {
        Route::resource('accounting/journal-entries', JournalEntryController::class);
        Route::post('accounting/journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
        Route::post('accounting/journal-entries/{journalEntry}/void', [JournalEntryController::class, 'void'])->name('journal-entries.void');
    });

    // Sales
    Route::name('sales.')->group(function () {
        Route::resource('sales/quotations', SalesQuotationController::class);
        Route::post('sales/quotations/{quotation}/submit', [SalesQuotationController::class, 'submit'])->name('quotations.submit');
        Route::post('sales/quotations/{quotation}/approve', [SalesQuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('sales/quotations/{quotation}/reject', [SalesQuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('sales/quotations/{quotation}/convert-to-so', [SalesQuotationController::class, 'convertToSO'])->name('quotations.convert-to-so');

        Route::resource('sales/orders', SalesOrderController::class);
        Route::post('sales/orders/{order}/submit', [SalesOrderController::class, 'submit'])->name('orders.submit');
        Route::post('sales/orders/{order}/approve', [SalesOrderController::class, 'approve'])->name('orders.approve');
        Route::post('sales/orders/{order}/cancel', [SalesOrderController::class, 'cancel'])->name('orders.cancel');

        Route::resource('sales/invoices', SalesInvoiceController::class);
        Route::get('sales/invoices/{invoice}/print', [SalesInvoiceController::class, 'print'])->name('invoices.print');
        Route::post('sales/invoices/{invoice}/submit', [SalesInvoiceController::class, 'submit'])->name('invoices.submit');
        Route::post('sales/invoices/{invoice}/approve', [SalesInvoiceController::class, 'approve'])->name('invoices.approve');
        Route::post('sales/invoices/{invoice}/post', [SalesInvoiceController::class, 'post'])->name('invoices.post');
        Route::post('sales/invoices/{invoice}/void', [SalesInvoiceController::class, 'void'])->name('invoices.void');

        Route::resource('sales/customer-payments', CustomerPaymentController::class);
        Route::post('sales/customer-payments/{customerPayment}/post', [CustomerPaymentController::class, 'post'])->name('customer-payments.post');

        Route::resource('sales/returns', SalesReturnController::class);
        Route::post('sales/returns/{return}/post', [SalesReturnController::class, 'post'])->name('sales-returns.post');
    });

    // Purchasing
    Route::name('purchasing.')->group(function () {
        Route::resource('purchasing/requests', PurchaseRequestController::class)->parameters(['requests' => 'purchaseRequest']);
        Route::post('purchasing/requests/{purchaseRequest}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
        Route::post('purchasing/requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
        Route::post('purchasing/requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');

        Route::resource('purchasing/orders', PurchaseOrderController::class);
        Route::get('purchasing/orders/{order}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
        Route::post('purchasing/orders/{order}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
        Route::post('purchasing/orders/{order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
        Route::post('purchasing/orders/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');

        Route::resource('purchasing/received-goods', ReceivedGoodsController::class)->parameters(['received-goods' => 'receivedGoods']);
        Route::resource('purchasing/invoices', PurchaseInvoiceController::class);
        Route::post('purchasing/invoices/{invoice}/submit', [PurchaseInvoiceController::class, 'submit'])->name('purchase-invoices.submit');
        Route::post('purchasing/invoices/{invoice}/approve', [PurchaseInvoiceController::class, 'approve'])->name('purchase-invoices.approve');
        Route::post('purchasing/invoices/{invoice}/post', [PurchaseInvoiceController::class, 'post'])->name('purchase-invoices.post');
        Route::post('purchasing/invoices/{invoice}/void', [PurchaseInvoiceController::class, 'void'])->name('purchase-invoices.void');

        Route::resource('purchasing/supplier-payments', SupplierPaymentController::class);
        Route::post('purchasing/supplier-payments/{supplierPayment}/post', [SupplierPaymentController::class, 'post'])->name('supplier-payments.post');
    });

    // Inventory
    Route::name('inventory.')->group(function () {
        Route::resource('inventory/stock-opnames', StockOpnameController::class);
        Route::post('inventory/stock-opnames/{opname}/process', [StockOpnameController::class, 'process'])->name('stock-opnames.process');
        Route::post('inventory/stock-opnames/{opname}/generate-adjustment', [StockOpnameController::class, 'generateAdjustment'])->name('stock-opnames.generate-adjustment');

        Route::resource('inventory/stock-adjustments', StockAdjustmentController::class);
        Route::post('inventory/stock-adjustments/{adjustment}/post', [StockAdjustmentController::class, 'post'])->name('stock-adjustments.post');

        Route::get('inventory/movements', [InventoryMovementController::class, 'index'])->name('movements.index');
    });

    // Production
    Route::name('production.')->group(function () {
        Route::resource('production/assemblies', AssemblyController::class);
        Route::post('production/assemblies/{assembly}/post', [AssemblyController::class, 'post'])->name('assemblies.post');
    });

    // Reports
    Route::get('reports/cash-book', [CashBookController::class, 'index'])->name('reports.cash-book');
    Route::get('reports/cash-book/export-pdf', [CashBookController::class, 'exportPdf'])->name('reports.cash-book.export-pdf');
    Route::get('reports/cash-book/export-excel', [CashBookController::class, 'exportExcel'])->name('reports.cash-book.export-excel');
    Route::get('reports/general-ledger', [GeneralLedgerController::class, 'index'])->name('reports.general-ledger');
    Route::get('reports/trial-balance', [TrialBalanceController::class, 'index'])->name('reports.trial-balance');
    Route::get('reports/balance-sheet', [BalanceSheetController::class, 'index'])->name('reports.balance-sheet');
    Route::get('reports/income-statement', [IncomeStatementController::class, 'index'])->name('reports.income-statement');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
