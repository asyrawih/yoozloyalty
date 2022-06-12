<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditTableCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('codes', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->integer('bill_amount')->nullable()->change();
            $table->string('bill_number')->nullable()->change();
        });
        DB::statement('ALTER TABLE `codes` ADD `uuid` VARCHAR(191) UNIQUE NULL AFTER `id`;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('codes', function (Blueprint $table) {            
            $table->integer('bill_amount')->change();
            $table->string('bill_number')->change();
        });
        DB::statement('ALTER TABLE `codes` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');        
    }
}
