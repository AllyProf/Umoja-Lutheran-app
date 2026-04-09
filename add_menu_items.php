<?php
require __DIR__ . '/vendor/autoload.php';
use Illuminate\Contracts\Console\Kernel;
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$items = [
    // Juices
    ['name' => 'Juice Mchanganyiko', 'price' => 2000, 'cat' => 'juices'],
    ['name' => 'Tende+Korosho+Maziwa', 'price' => 4000, 'cat' => 'juices'],
    ['name' => 'Fruit Salad', 'price' => 3000, 'cat' => 'juices'],

    // Chai/Tea
    ['name' => 'Chai Maziwa', 'price' => 1500, 'cat' => 'chai'],
    ['name' => 'Chai Rangi', 'price' => 1000, 'cat' => 'chai'],
    ['name' => 'Black Coffee', 'price' => 2000, 'cat' => 'chai'],
    ['name' => 'Hot Lemon', 'price' => 4000, 'cat' => 'chai'],
    ['name' => 'Black Coffee with Milk', 'price' => 3000, 'cat' => 'chai'],

    // Bites
    ['name' => 'Beef Bite', 'price' => 3000, 'cat' => 'bites'],
    ['name' => 'Fish Bites', 'price' => 12000, 'cat' => 'bites'],
    ['name' => 'Chicken Bites', 'price' => 7000, 'cat' => 'bites'],
    ['name' => 'Chapati', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Samosa', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Kababu', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Donati', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Andazi', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Shankeli', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Sausage', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Mayai', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Mkate', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Kokoto', 'price' => 1000, 'cat' => 'bites'],
    ['name' => 'Karanga', 'price' => 1500, 'cat' => 'bites'],
    ['name' => 'Ufuta', 'price' => 1500, 'cat' => 'bites'],

    // Traditional / Food
    ['name' => 'Soup Kienyeji', 'price' => 10000, 'cat' => 'food'],
    ['name' => 'Mtori', 'price' => 3000, 'cat' => 'traditional'],
    ['name' => 'Ngararimo', 'price' => 4000, 'cat' => 'traditional'],
    ['name' => 'Kiburu', 'price' => 4000, 'cat' => 'traditional'],
    ['name' => 'Ngande Maharage', 'price' => 4000, 'cat' => 'traditional'],
    ['name' => 'Ngande Choroko', 'price' => 4000, 'cat' => 'traditional'],
];

foreach ($items as $item) {
    \App\Models\Recipe::updateOrCreate(
        ['name' => $item['name']],
        ['selling_price' => $item['price'], 'category' => $item['cat'], 'is_available' => true]
    );
}

echo "Successfully added/updated menu items.";
