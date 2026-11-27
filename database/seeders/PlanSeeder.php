<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['name' => 'ADVOCACY PLAN 649/45MBPS'],
            [
                'profile_group' => 'pppoe-45mbit/649',
                'transaction_type' => 'mrc',
                'speed_mbps' => 45,
                'price' => '649.00',
                'usage_count' => 106,
            ],
        );
    }
}
