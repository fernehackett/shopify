<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer("store_id")->index();
            $table->string("order_id")->index();
            $table->string("order_number")->index();
            $table->json("customer")->nullable();
            $table->string("financial_status");
            $table->string("fulfillment_status")->nullable();
            $table->string("name");
            $table->text("note")->nullable();
            $table->json("note_attributes")->nullable();
            $table->json("payment_details");
            $table->string("phone")->nullable();
            $table->json("shipping_address");
            $table->string("token");
            $table->decimal("total_price");
            $table->string("order_status_url");
            $table->json("shipping_lines");
            $table->timestamp("created_at");
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
        Schema::dropIfExists('orders');
    }
}
