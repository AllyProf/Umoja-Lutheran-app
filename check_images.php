<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recipe = \App\Models\Recipe::whereNotNull('image')->first();
if ($recipe) {
    echo "Image Path: " . $recipe->image . "\n";
    $fullPath = storage_path('app/public/' . $recipe->image);
    echo "Full Path: " . $fullPath . "\n";
    echo "File Exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
} else {
    echo "No recipes with images found.\n";
}
