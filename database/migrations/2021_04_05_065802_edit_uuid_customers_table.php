<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditUuidCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->string('country_code', 10)->change();
        });
        DB::statement('ALTER TABLE `customers` ADD `uuid` VARCHAR(191) UNIQUE NULL AFTER `id`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `customers` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');        
    }
}
