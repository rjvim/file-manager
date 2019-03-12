<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_links', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('media_library_id')->unsigned()->nullable();
            $table->integer('of_id')->nullable();
            $table->string('of_type')->nullable();
            $table->string('type')->nullable();

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
        Schema::dropIfExists('media_links');
    }
}