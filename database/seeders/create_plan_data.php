<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Plan::create([
           "type" => "RECURRING",
            "name" => "Monthly",
            "price" => 9.99,
            "interval" => "EVERY_30_DAYS",
            "capped_amount" =>9.99,
            "terms" => "A monthly charge to use service. Sometimes referred to as a maintenance fee.",
            "trial_days" => 1,
            "test" => false,
            "on_install" => 1,
            "created_at" => null,
            "updated_at" => null
        ]);
    }
}
