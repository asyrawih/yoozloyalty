<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangePlanRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_change_requests', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->id();
            $table->integer('previous_plan_id')->unsigned()->nullable();
            $table->foreign('previous_plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->integer('new_plan_id')->unsigned()->nullable();
            $table->foreign('new_plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->bigInteger('requested_by')->unsigned()->index();
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('approved_by')->unsigned()->nullable()->index();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['approved', 'rejected', 'cancelled', 'pending'])->default('pending');
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
        Schema::dropIfExists('plan_change_requests');
    }
}
