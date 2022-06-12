<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReceiptOnCreditRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_requests', function (Blueprint $table) {
            $table->string('receipt_number')
                ->after('campaign_id')
                ->default('Not Recorded');

            $table->unsignedBigInteger('receipt_amount')
                ->after('receipt_number')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_requests', function (Blueprint $table) {
            $table->dropColumn(['receipt_number', 'receipt_amount']);
        });
    }
}
