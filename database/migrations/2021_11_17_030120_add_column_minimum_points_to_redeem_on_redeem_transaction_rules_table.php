<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMinimumPointsToRedeemOnRedeemTransactionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_transaction_rules', function (Blueprint $table) {
            $table->unsignedInteger('minimum_points')->after('value')->default(0);
            $table->unsignedInteger('maximum_redeem')->after('minimum_points')->default(0);
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
            $table->dropColumn([
                'minimum_points',
                'maximum_redeem'
            ]);
        });
    }
}
