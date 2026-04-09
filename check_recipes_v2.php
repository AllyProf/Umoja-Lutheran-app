<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Recipe;

$recipes = Recipe::where('is_available', true)->get();
foreach ($recipes as $r) {
    echo "ID: {$r->id} | Name: {$r->name} | Category: {$r->category} | Price: {$r->selling_price}\n";
}
