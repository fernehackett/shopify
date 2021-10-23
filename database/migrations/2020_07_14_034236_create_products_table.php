<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer("store_id")->index();
            $table->string("product_id")->index();
            $table->string("title");
            $table->string("link");
            $table->string("short_link")->nullable();
            $table->string("banner")->nullable();
            $table->string("product_type")->nullable();
            $table->string("vendor")->nullable();
            $table->longText("tags")->nullable();
            $table->longText("description")->nullable();
            $table->tinyInteger("status")->default(0);
            $table->string("fbpixel")->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('products');
    }
}
