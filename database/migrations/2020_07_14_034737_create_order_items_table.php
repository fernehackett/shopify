<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer("store_id");
            $table->integer("order_id");
            $table->string("item_id")->index();
            $table->integer("fulfillable_quantity");
            $table->string("fulfillment_status")->nullable();
            $table->string("product_id")->index();
            $table->integer("quantity");
            $table->string("sku")->nullable();
            $table->string("title");
            $table->string("variant_id");
            $table->string("variant_title");
            $table->string("vendor");
            $table->json("properties");
            $table->string("order_number")->index();
	        $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
