<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            // Self Contained
            [
                'room_number' => 'SC-101',
                'room_type' => 'Self-Contained Single',
                'capacity' => 1,
                'bed_type' => 'Single',
                'price_per_night' => 30000,
                'status' => 'available',
                'description' => 'Self-contained single room with private bathroom.',
            ],
            [
                'room_number' => 'SC-102',
                'room_type' => 'Self-Contained Double',
                'capacity' => 2,
                'bed_type' => 'King',
                'price_per_night' => 50000,
                'status' => 'available',
                'description' => 'Self-contained double room with shared king bed.',
            ],

            // Standard Room
            [
                'room_number' => 'STD-201',
                'room_type' => 'Standard Single',
                'capacity' => 1,
                'bed_type' => 'Single',
                'price_per_night' => 15000,
                'status' => 'available',
                'description' => 'Standard single room.',
            ],
            [
                'room_number' => 'STD-202',
                'room_type' => 'Standard Double',
                'capacity' => 2,
                'bed_type' => 'Queen',
                'price_per_night' => 30000,
                'status' => 'available',
                'description' => 'Standard double room.',
            ],
            [
                'room_number' => 'STD-203',
                'room_type' => 'Standard Triple',
                'capacity' => 3,
                'bed_type' => 'Twin',
                'price_per_night' => 45000,
                'status' => 'available',
                'description' => 'Standard triple room with three twin beds.',
            ],
            [
                'room_number' => 'STD-204',
                'room_type' => 'Standard Decker',
                'capacity' => 4,
                'bed_type' => 'Bunk',
                'price_per_night' => 60000,
                'status' => 'available',
                'description' => 'Standard decker room with bunk beds.',
            ],

            // En-suite Rooms
            [
                'room_number' => 'EN-301',
                'room_type' => 'En-suite Single',
                'capacity' => 1,
                'bed_type' => 'King',
                'price_per_night' => 60000,
                'status' => 'available',
                'description' => 'En-suite single room (Suite House).',
            ],
            [
                'room_number' => 'EN-302',
                'room_type' => 'En-suite Triple',
                'capacity' => 3,
                'bed_type' => 'Twin',
                'price_per_night' => 90000,
                'status' => 'available',
                'description' => 'En-suite family house with 3 beds.',
            ],
            [
                'room_number' => 'EN-303',
                'room_type' => 'En-suite Quad',
                'capacity' => 4,
                'bed_type' => 'Single',
                'price_per_night' => 120000,
                'status' => 'available',
                'description' => 'En-suite suite family with 4 beds.',
            ],
            [
                'room_number' => 'EN-304',
                'room_type' => 'En-suite Quint',
                'capacity' => 5,
                'bed_type' => 'Single',
                'price_per_night' => 150000,
                'status' => 'available',
                'description' => 'En-suite family house with 5 beds.',
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::updateOrCreate(
                ['room_number' => $roomData['room_number']],
                $roomData
            );
        }
    }
}
