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

            // Simplified Types
            [
                'room_number' => '101',
                'room_type' => 'Single',
                'capacity' => 1,
                'bed_type' => 'Single',
                'price_per_night' => 20000,
                'status' => 'available',
                'description' => 'Single room.',
            ],
            [
                'room_number' => '201',
                'room_type' => 'Double',
                'capacity' => 2,
                'bed_type' => 'Queen',
                'price_per_night' => 35000,
                'status' => 'available',
                'description' => 'Double room.',
            ],
            [
                'room_number' => '301',
                'room_type' => 'Twins',
                'capacity' => 2,
                'bed_type' => 'Twin',
                'price_per_night' => 40000,
                'status' => 'available',
                'description' => 'Standard twin room with two twin beds.',
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
