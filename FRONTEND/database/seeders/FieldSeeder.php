<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'KickSwift Arena 1 - Vinyl Pro',
                'slug' => 'kickswift-arena-1-vinyl-pro',
                'type' => 'vinyl',
                'description' => 'Lapangan lantai vinyl standar internasional dengan peredam kejut tinggi, sangat aman untuk lutut dan pergelangan kaki. Dilengkapi pencahayaan LED stadion.',
                'price_per_hour' => 150000.00,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'KickSwift Arena 2 - Synthetic Grass',
                'slug' => 'kickswift-arena-2-synthetic-grass',
                'type' => 'synthetic_grass',
                'description' => 'Lapangan rumput sintetis monofilamen premium dengan rubber infill berkualitas tinggi, memberikan sensasi bermain sepak bola sungguhan.',
                'price_per_hour' => 175000.00,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'KickSwift Arena 3 - Interlocking Floor',
                'slug' => 'kickswift-arena-3-interlocking-floor',
                'type' => 'interlocking',
                'description' => 'Lapangan interlocking modular fleksibel dengan grip maksimal anti-slip, cocok untuk pertandingan turnamen dan latihan intensif.',
                'price_per_hour' => 130000.00,
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($fields as $fieldData) {
            $field = Field::updateOrCreate(
                ['slug' => $fieldData['slug']],
                $fieldData
            );

            // Buat slot jadwal operasional dari jam 08:00 hingga 23:00 untuk setiap hari (0-6)
            for ($day = 0; $day <= 6; $day++) {
                for ($hour = 8; $hour < 23; $hour++) {
                    $startTime = sprintf('%02d:00:00', $hour);
                    $endTime = sprintf('%02d:00:00', $hour + 1);

                    // Peak hour pricing (18:00 ke atas bertambah 25.000)
                    $customPrice = ($hour >= 18) ? ($field->price_per_hour + 25000.00) : null;

                    Schedule::updateOrCreate(
                        [
                            'field_id' => $field->id,
                            'day_of_week' => $day,
                            'start_time' => $startTime,
                        ],
                        [
                            'end_time' => $endTime,
                            'custom_price' => $customPrice,
                            'is_available' => true,
                        ]
                    );
                }
            }
        }
    }
}
