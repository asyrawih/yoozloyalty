<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsExpiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points_expiries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_user_id')->unique()->unsigned()->nullable();
            $table->integer('points_expiry')->unsigned()->default(30);
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
        Schema::dropIfExists('points_expiries');
    }
}
