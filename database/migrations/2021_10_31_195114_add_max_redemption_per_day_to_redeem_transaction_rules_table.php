<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxRedemptionPerDayToRedeemTransactionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_transaction_rules', function (Blueprint $table) {
            $table->integer('max_redemption_per_day')->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeem_transaction_rules', function (Blueprint $table) {
            $table->dropColumn('max_redemption_per_day');
        });
    }
}
