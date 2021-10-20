<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
	        $table->integer("store_id")->index();
	        $table->string("product_id")->index();
            $table->string("variant_id")->index();
            $table->string("title");
            $table->string("sku")->nullable();
            $table->integer("position")->default(1);
            $table->string("fulfillment_service")->nullable();
            $table->string("option1")->nullable();
            $table->string("option2")->nullable();
            $table->string("option3")->nullable();
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
        Schema::dropIfExists('product_variants');
    }
}
