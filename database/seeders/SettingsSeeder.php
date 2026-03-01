<?php

namespace Database\Seeders;

use App\Models\Settings;
use App\Models\ShippingFee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::factory()->create([
            'site_unlock_password' => Hash::make('Ripple!!Secure374mnb'),
        ]);
        ShippingFee::create([
            'fees' => json_encode([
                [
                    'currency' => 'USD',
                    'amount' => 2
                ],
                [
                    'currency' => 'NGN',
                    'amount' => 1500
                ],
            ])
        ]);
    }
}
