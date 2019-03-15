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
            $table->string('mime_type')->nullable();
            $table->string('disk')->nullable(); //s3, cloudinary
            $table->string('path')->nullable();
            $table->integer('uploaded_by')->unsigned()->nullable();

            $table->text('meta')->nullable(); // {file_name, summary, description}
            $table->text('tags')->nullable(); // ["fishing","photography"]

            /**
                LIKE %fishing% OR LIKE %photo%
            **/

            $table->softDeletes();
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
