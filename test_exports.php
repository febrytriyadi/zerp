<?php

namespace App\Http;

use App\Models\Purchasing\PurchaseOrder;
use App\Models\Sales\SalesInvoice;
use App\Models\User;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$kernel->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class);

$user = User::first();
if (!$user) {
    echo "No user found. Run: php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder\n";
    exit(1);
}

// Simulate login
$this->app = $app;
$response = [];

// Test export endpoints
$routes = [
    'Companies Excel' => ['master.companies.export-excel', []],
    'Customers Excel' => ['master.customers.export-excel', []],
    'Suppliers Excel' => ['master.suppliers.export-excel', []],
    'Products Excel' => ['master.products.export-excel', []],
    'Cash Book Excel' => ['reports.cash-book.export-excel', []],
    'Cash Book PDF' => ['reports.cash-book.export-pdf', []],
];

foreach ($routes as $label => [$name, $params]) {
    try {
        $url = route($name, $params);
        $req = Request::create($url, 'GET');
        $req->setUserResolver(fn() => $user);
        $res = $kernel->handle($req);
        echo "$label: {$res->getStatusCode()} {$res->headers->get('content-type')}\n";
    } catch (\Exception $e) {
        echo "$label: ERROR - {$e->getMessage()}\n";
    }
}

// Test print endpoints
$invoice = SalesInvoice::first();
if ($invoice) {
    $url = route('sales.invoices.print', $invoice);
    $req = Request::create($url, 'GET');
    $req->setUserResolver(fn() => $user);
    $res = $kernel->handle($req);
    echo "Invoice Print: {$res->getStatusCode()}\n";
}

$order = PurchaseOrder::first();
if ($order) {
    $url = route('purchasing.purchase-orders.print', $order);
    $req = Request::create($url, 'GET');
    $req->setUserResolver(fn() => $user);
    $res = $kernel->handle($req);
    echo "PO Print: {$res->getStatusCode()}\n";
}

echo "\nAll export/print tests completed.\n";
