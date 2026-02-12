<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Transport Services
            [
                'name' => 'Airport Pickup',
                'description' => 'Pickup service from airport to hotel',
                'category' => 'transport',
                'price_tsh' => 50000,
                'unit' => 'per_trip',
                'is_active' => true,
                'requires_approval' => true,
                'required_fields' => [
                    [
                        'name' => 'arrival_date',
                        'label' => 'Arrival Date',
                        'type' => 'date',
                        'required' => true,
                    ],
                    [
                        'name' => 'arrival_time',
                        'label' => 'Arrival Time',
                        'type' => 'time',
                        'required' => true,
                    ],
                    [
                        'name' => 'flight_number',
                        'label' => 'Flight Number / Airline Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'e.g., EK 723, Qatar Airways QR 801',
                    ],
                    [
                        'name' => 'airport_name',
                        'label' => 'Airport Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'e.g., Julius Nyerere International Airport',
                    ],
                    [
                        'name' => 'number_of_passengers',
                        'label' => 'Number of Passengers',
                        'type' => 'number',
                        'required' => true,
                        'min' => 1,
                        'default' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Airport Drop-off',
                'description' => 'Drop-off service from hotel to airport',
                'category' => 'transport',
                'price_tsh' => 50000,
                'unit' => 'per_trip',
                'is_active' => true,
                'requires_approval' => true,
                'required_fields' => [
                    [
                        'name' => 'departure_date',
                        'label' => 'Departure Date',
                        'type' => 'date',
                        'required' => true,
                    ],
                    [
                        'name' => 'departure_time',
                        'label' => 'Departure Time',
                        'type' => 'time',
                        'required' => true,
                    ],
                    [
                        'name' => 'flight_number',
                        'label' => 'Flight Number / Airline Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'e.g., EK 723, Qatar Airways QR 801',
                    ],
                    [
                        'name' => 'airport_name',
                        'label' => 'Airport Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'e.g., Julius Nyerere International Airport',
                    ],
                    [
                        'name' => 'number_of_passengers',
                        'label' => 'Number of Passengers',
                        'type' => 'number',
                        'required' => true,
                        'min' => 1,
                        'default' => 1,
                    ],
                ],
            ],
            [
                'name' => 'City Tour Transportation',
                'description' => 'Transportation for city tour',
                'category' => 'transport',
                'price_tsh' => 80000,
                'unit' => 'per_day',
                'is_active' => true,
                'requires_approval' => true,
            ],

            // Food & Beverage Services
            [
                'name' => 'Room Service Breakfast',
                'description' => 'Breakfast delivered to your room',
                'category' => 'food',
                'price_tsh' => 25000,
                'unit' => 'per_person',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Room Service Lunch',
                'description' => 'Lunch delivered to your room',
                'category' => 'food',
                'price_tsh' => 35000,
                'unit' => 'per_person',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Room Service Dinner',
                'description' => 'Dinner delivered to your room',
                'category' => 'food',
                'price_tsh' => 40000,
                'unit' => 'per_person',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Mini Bar Refill',
                'description' => 'Refill of mini bar items',
                'category' => 'food',
                'price_tsh' => 30000,
                'unit' => 'per_refill',
                'is_active' => true,
                'requires_approval' => false,
            ],

            // Laundry Services
            [
                'name' => 'Laundry Service',
                'description' => 'Professional laundry service',
                'category' => 'laundry',
                'price_tsh' => 15000,
                'unit' => 'per_kg',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Dry Cleaning',
                'description' => 'Dry cleaning service',
                'category' => 'laundry',
                'price_tsh' => 20000,
                'unit' => 'per_item',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Ironing Service',
                'description' => 'Professional ironing service',
                'category' => 'laundry',
                'price_tsh' => 10000,
                'unit' => 'per_item',
                'is_active' => true,
                'requires_approval' => false,
            ],

            // Spa & Wellness
            [
                'name' => 'Massage Therapy',
                'description' => 'Professional massage therapy',
                'category' => 'spa',
                'price_tsh' => 80000,
                'unit' => 'per_session',
                'is_active' => true,
                'requires_approval' => true,
                'required_fields' => [
                    [
                        'name' => 'preferred_date',
                        'label' => 'Preferred Date',
                        'type' => 'date',
                        'required' => true,
                    ],
                    [
                        'name' => 'preferred_time',
                        'label' => 'Preferred Time',
                        'type' => 'time',
                        'required' => true,
                    ],
                    [
                        'name' => 'service_location',
                        'label' => 'Service Location',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'room', 'label' => 'In Room'],
                            ['value' => 'spa', 'label' => 'Spa Facility'],
                        ],
                    ],
                    [
                        'name' => 'room_number',
                        'label' => 'Room Number (if in room)',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'Enter room number if service is in room',
                    ],
                    [
                        'name' => 'massage_type',
                        'label' => 'Massage Type',
                        'type' => 'select',
                        'required' => false,
                        'options' => [
                            ['value' => 'swedish', 'label' => 'Swedish Massage'],
                            ['value' => 'deep_tissue', 'label' => 'Deep Tissue'],
                            ['value' => 'hot_stone', 'label' => 'Hot Stone'],
                            ['value' => 'aromatherapy', 'label' => 'Aromatherapy'],
                            ['value' => 'other', 'label' => 'Other'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Spa Treatment',
                'description' => 'Full spa treatment package',
                'category' => 'spa',
                'price_tsh' => 150000,
                'unit' => 'per_session',
                'is_active' => true,
                'requires_approval' => true,
                'required_fields' => [
                    [
                        'name' => 'preferred_date',
                        'label' => 'Preferred Date',
                        'type' => 'date',
                        'required' => true,
                    ],
                    [
                        'name' => 'preferred_time',
                        'label' => 'Preferred Time',
                        'type' => 'time',
                        'required' => true,
                    ],
                    [
                        'name' => 'service_location',
                        'label' => 'Service Location',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'spa', 'label' => 'Spa Facility'],
                            ['value' => 'room', 'label' => 'In Room'],
                        ],
                    ],
                    [
                        'name' => 'room_number',
                        'label' => 'Room Number (if in room)',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'Enter room number if service is in room',
                    ],
                ],
            ],

            // Other Services
            [
                'name' => 'Extra Towels',
                'description' => 'Additional towels for your room',
                'category' => 'room_service',
                'price_tsh' => 5000,
                'unit' => 'per_set',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Extra Bedding',
                'description' => 'Additional bedding for your room',
                'category' => 'room_service',
                'price_tsh' => 10000,
                'unit' => 'per_set',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Wake-up Call',
                'description' => 'Wake-up call service',
                'category' => 'room_service',
                'price_tsh' => 0,
                'unit' => 'per_call',
                'is_active' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Late Check-out',
                'description' => 'Extended check-out time (until 2 PM)',
                'category' => 'room_service',
                'price_tsh' => 30000,
                'unit' => 'per_request',
                'is_active' => true,
                'requires_approval' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
