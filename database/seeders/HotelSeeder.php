<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\Extra;
use App\Models\Housekeeping;
use Illuminate\Support\Facades\Hash;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@beztower.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Hotel Manager',
            'email' => 'manager@beztower.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager'
        ]);

        User::create([
            'name' => 'Front Desk',
            'email' => 'receptionist@beztower.com',
            'password' => Hash::make('receptionist123'),
            'role' => 'receptionist'
        ]);

        // Create Room Types
        $standard = RoomType::create([
            'name' => 'Standard Room',
            'description' => 'Comfortable and cozy room perfect for solo travelers or couples. Features modern amenities and elegant decor.',
            'base_price' => 2500.00,
            'max_guests' => 2
        ]);

        $deluxe = RoomType::create([
            'name' => 'Deluxe Room',
            'description' => 'Spacious room with premium furnishings and stunning city views. Ideal for families or those seeking extra comfort.',
            'base_price' => 4500.00,
            'max_guests' => 3
        ]);

        $suite = RoomType::create([
            'name' => 'Executive Suite',
            'description' => 'Luxurious suite featuring separate living area, king-size bed, and exclusive amenities. Perfect for extended stays or special occasions.',
            'base_price' => 8000.00,
            'max_guests' => 4
        ]);

        // Create Amenities
        $amenities = [
            'WiFi', 'Air Conditioning', 'LED TV', 'Mini Bar', 'Coffee Maker',
            'Safe Box', 'Work Desk', 'Balcony', 'City View', 'Ocean View',
            'Bathtub', 'Rain Shower', 'Hair Dryer', 'Telephone', 'Room Service'
        ];

        $amenityIds = [];
        foreach ($amenities as $amenity) {
            $amenityIds[] = Amenity::create(['name' => $amenity])->id;
        }

        // Create Rooms - Standard
        for ($i = 0; $i < 5; $i++) {
            $room = Room::create([
                'room_type_id' => $standard->id,
                'room_number' => (101 + $i),
                'floor' => 1,
                'description' => 'Well-appointed standard room',
                'status' => 'available'
            ]);
            $room->amenities()->attach([$amenityIds[0], $amenityIds[1], $amenityIds[2], $amenityIds[6], $amenityIds[11], $amenityIds[12]]);
            Housekeeping::create(['room_id' => $room->id, 'status' => 'clean', 'last_cleaned_at' => now()]);
        }

        // Deluxe Rooms
        for ($i = 0; $i < 5; $i++) {
            $room = Room::create([
                'room_type_id' => $deluxe->id,
                'room_number' => (201 + $i),
                'floor' => 2,
                'description' => 'Premium deluxe room with enhanced amenities',
                'status' => 'available'
            ]);
            $room->amenities()->attach([$amenityIds[0], $amenityIds[1], $amenityIds[2], $amenityIds[3], $amenityIds[4], $amenityIds[5], $amenityIds[6], $amenityIds[7], $amenityIds[8], $amenityIds[11], $amenityIds[12], $amenityIds[14]]);
            Housekeeping::create(['room_id' => $room->id, 'status' => 'clean', 'last_cleaned_at' => now()]);
        }

        // Executive Suites
        for ($i = 0; $i < 5; $i++) {
            $room = Room::create([
                'room_type_id' => $suite->id,
                'room_number' => (301 + $i),
                'floor' => 3,
                'description' => 'Luxurious executive suite with premium facilities',
                'status' => 'available'
            ]);
            $room->amenities()->attach($amenityIds);
            Housekeeping::create(['room_id' => $room->id, 'status' => 'clean', 'last_cleaned_at' => now()]);
        }

        // Create/Update Extras
        Extra::updateOrCreate(
            ['name' => 'Extra Bed'],
            ['description' => 'Additional extra bed setup', 'price' => 800.00, 'is_active' => true]
        );
        Extra::updateOrCreate(
            ['name' => 'Extra Bedding'],
            ['description' => 'Includes 2 pillows, 1 bedding, and 1 cover', 'price' => 300.00, 'is_active' => true]
        );
        Extra::updateOrCreate(
            ['name' => 'Soap, Shampoo, Conditioner'],
            ['description' => 'Toiletries set per request', 'price' => 50.00, 'is_active' => true]
        );
        Extra::updateOrCreate(
            ['name' => 'Towel'],
            ['description' => 'Additional towel set', 'price' => 200.00, 'is_active' => true]
        );
        Extra::updateOrCreate(
            ['name' => 'Motorcycle Parking'],
            ['description' => 'Motorcycle parking per night', 'price' => 100.00, 'is_active' => true]
        );
        Extra::updateOrCreate(
            ['name' => 'Car Parking'],
            ['description' => 'Car parking per night', 'price' => 200.00, 'is_active' => true]
        );
    

        $this->command->info('Hotel data seeded successfully!');
    }
}
