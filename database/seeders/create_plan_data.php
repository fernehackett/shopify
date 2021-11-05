<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Osiset\ShopifyApp\Storage\Models\Plan;

class create_plan_data extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::truncate();
        Plan::create([
           "type" => "RECURRING",
            "name" => "Monthly",
            "price" => 9.99,
            "interval" => "EVERY_30_DAYS",
            "capped_amount" =>14.99,
            "terms" => "monthly",
            "trial_days" => 1,
            "test" => false,
            "on_install" => 1,
            "created_at" => null,
            "updated_at" => null
        ]);
        Plan::create([
           "type" => "RECURRING",
            "name" => "Yearly",
            "price" => 99.99,
            "interval" => "ANNUAL",
            "capped_amount" =>149.99,
            "terms" => "yearly",
            "trial_days" => 1,
            "test" => false,
            "on_install" => 1,
            "created_at" => null,
            "updated_at" => null
        ]);
    }
}
