<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class FinalizeShoppingListTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the latest pending list (likely the one we just created)
        $list = ShoppingList::where('status', 'pending')->latest()->first();

        if (!$list) {
            $this->command->error('No pending shopping list found to finalize.');
            return;
        }

        $this->command->info('Finalizing list: ' . $list->name);

        foreach ($list->items as $item) {
            // Simulate receiving the items with maybe slightly different quantities
            $actualQty = $item->quantity;
            $actualCost = $item->estimated_price;

            if ($item->unit == 'sado') {
                $actualQty = 3; // Planned 2, bought 3
                $actualCost = 30000;
            }

            $item->update([
                'purchased_quantity' => $actualQty,
                'purchased_cost' => $actualCost,
                'is_purchased' => true,
                'storage_location' => 'Kitchen Main Fridge'
            ]);
        }

        $list->update([
            'status' => 'completed',
            'total_actual_cost' => $list->items()->sum('purchased_cost')
        ]);

        $this->command->info('Shopping List finalized and Stock updated!');
    }
}
