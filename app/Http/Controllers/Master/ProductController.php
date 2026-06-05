<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\ProductExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Company;
use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'unit')->paginate(10);
        return view('master.products.index', compact('products'));
    }

    public function create()
    {
        $companies = Company::all();
        $categories = ProductCategory::all();
        $units = Unit::all();
        return view('master.products.create', compact('companies', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'cost_method' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('master.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        $categories = ProductCategory::all();
        $units = Unit::all();
        return view('master.products.edit', compact('product', 'companies', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'cost_method' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('master.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('master.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function exportExcel()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
}
