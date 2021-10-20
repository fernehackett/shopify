<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fulfillments', function (Blueprint $table) {
            $table->id();
            $table->string('store_id');
            $table->string('order_id');
            $table->string('fulfillment_id');
            $table->string('tracking_company');
            $table->json('tracking_numbers');
            $table->json('tracking_urls');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fulfillments');
    }
}
