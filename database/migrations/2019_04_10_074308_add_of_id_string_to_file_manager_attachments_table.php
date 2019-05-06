<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOfIdStringToFileManagerAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "public"."file_manager_attachments" ALTER COLUMN "of_id" TYPE character varying(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE "public"."file_manager_attachments" ALTER COLUMN "of_id" TYPE character varying(255)');
    }
}
