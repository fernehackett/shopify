<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_tags', function (Blueprint $table) {
            $table->id();
            $table->string("shopify_url");
            $table->string("name");
            $table->string("script_id");
            $table->string("src");
            $table->string("event");
            $table->boolean("cache");
            $table->string("display_scope");
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
        Schema::dropIfExists('script_tags');
    }
}
