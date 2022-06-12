<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCoupunCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupun_codes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('reward_id')->unsigned()->nullable()->index();
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->boolean('status')->default(0);


            $table->bigInteger('created_by')->unsigned()->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `coupun_codes` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupun_codes');
    }
}
