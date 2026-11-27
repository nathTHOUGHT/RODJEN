<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plan = Plan::query()->firstOrNew([
            'name' => 'ADVOCACY PLAN 649/45MBPS',
        ]);

        if (! $plan->exists) {
            $plan->usage_count = 106;
        }

        $plan->fill([
            'profile_group' => 'pppoe-45mbit/649',
            'transaction_type' => 'mrc',
            'speed_mbps' => 45,
            'price' => '649.00',
        ])->save();
    }
}
