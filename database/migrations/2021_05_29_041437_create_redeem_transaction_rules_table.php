<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeemTransactionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeem_transaction_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('value');

            $table->bigInteger('created_by')->unsigned()->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `redeem_transaction_rules` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redeem_transaction_rules');
    }
}
