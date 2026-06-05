<?php
namespace App\Services\Inventory;

use App\Models\Inventory\InventoryMovement;
use App\Models\Master\Product;
use Illuminate\Support\Facades\DB;

class AverageCostCalculator
{
    public function calculate(int $productId, int $warehouseId): float
    {
        $result = InventoryMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->selectRaw('SUM(total_cost) as total_cost, SUM(quantity_in - quantity_out) as total_quantity')
            ->first();

        if (!$result || $result->total_quantity <= 0) {
            return 0;
        }

        return $result->total_cost / $result->total_quantity;
    }

    public function recalculate(int $productId, ?int $warehouseId = null): void
    {
        $product = Product::findOrFail($productId);

        $query = InventoryMovement::where('product_id', $productId);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $result = $query
            ->selectRaw('SUM(total_cost) as total_cost, SUM(quantity_in - quantity_out) as total_quantity')
            ->first();

        $averageCost = 0;
        if ($result && $result->total_quantity > 0) {
            $averageCost = $result->total_cost / $result->total_quantity;
        }

        $product->average_cost = $averageCost;
        $product->save();
    }
}
