<?php
use Illuminate\Support\Facades\DB;

$tables = ['products', 'product_variants', 'services', 'recipes', 'service_catalog'];
foreach ($tables as $table) {
    try {
        $query = DB::table($table);
        if ($table === 'product_variants') {
            $query->where('variant_name', 'LIKE', '%Keepeng%')
                ->orWhere('variant_name', 'LIKE', '%Prtable%')
                ->orWhere('variant_name', 'LIKE', '%Beverage%')
                ->orWhere('variant_name', 'LIKE', '%House Keep%');
        } else {
            $query->where('name', 'LIKE', '%Keepeng%')
                ->orWhere('name', 'LIKE', '%Prtable%')
                ->orWhere('name', 'LIKE', '%Beverage%')
                ->orWhere('name', 'LIKE', '%House Keep%');
        }

        $results = $query->get();
        if ($results->count() > 0) {
            echo "MATCH IN $table:\n";
            foreach ($results as $row) {
                print_r($row);
            }
        }
    } catch (\Exception $e) {
        // Table might not have name/variant_name column
    }
}
