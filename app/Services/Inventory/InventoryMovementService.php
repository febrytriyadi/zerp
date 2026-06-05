<?php
namespace App\Services\Inventory;

use App\Models\Inventory\InventoryMovement;

class InventoryMovementService
{
    public function __construct(
        protected AverageCostCalculator $averageCostCalculator,
    ) {}

    public function recordMovement(array $data): InventoryMovement
    {
        $movement = InventoryMovement::create($data);

        if (in_array($data['transaction_type'], ['purchase_received', 'sales_delivery'])) {
            $this->averageCostCalculator->recalculate(
                $data['product_id'],
                $data['warehouse_id']
            );
        }

        return $movement;
    }
}
