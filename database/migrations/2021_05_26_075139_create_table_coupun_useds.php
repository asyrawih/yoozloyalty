<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCoupunUseds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupun_useds', function (Blueprint $table) {
            $table->id();            
            $table->bigInteger('reward_id')->unsigned()->nullable()->index();
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->bigInteger('coupun_code_id')->unsigned()->nullable()->index();
            $table->foreign('coupun_code_id')->references('id')->on('coupun_codes')->onDelete('cascade');
            $table->string('redemption_id');
            
            $table->bigInteger('created_by')->unsigned()->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_by')->nullable()->unsigned();
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
        Schema::dropIfExists('coupun_useds');
    }
}
