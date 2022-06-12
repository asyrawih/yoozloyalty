<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdPaypalInPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('product_id_paypal', 128)->after('product_id_stripe')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('previous_remote_gateway', 128)->after('previous_remote_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('previous_remote_gateway');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('previous_remote_gateway');
        });
    }
}
