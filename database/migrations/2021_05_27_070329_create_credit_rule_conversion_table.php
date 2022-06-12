<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditRuleConversionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_rule_conversions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('campaign_id')->unsigned()->index();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->bigInteger('min_amount');
            $table->bigInteger('max_amount');
            $table->enum('rule', ['fixed', 'percent']);
            $table->bigInteger('value');

            $table->bigInteger('created_by')->unsigned()->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `credit_rule_conversions` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_rule_conversions');
    }
}
