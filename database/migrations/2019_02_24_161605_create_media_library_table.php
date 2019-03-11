<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaLibraryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_library', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('uuid')->unique()->nullable();
            $table->integer('owner_id')->nullable();
            $table->string('owner_type')->nullable();
            $table->string('type')->nullable();
            $table->string('format')->nullable();
            $table->string('provider')->nullable();
            $table->string('path')->nullable();
            $table->integer('uploaded_by')->unsigned()->nullable();

            $table->string('filename')->nullable();
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
            $table->text('description')->nullable();
            $table->text('tags')->nullable();

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
        Schema::dropIfExists('media_library');
    }
}
