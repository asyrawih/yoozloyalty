<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->bigInteger('joining_fee')->after('meta')->unsigned()->default(0);
            $table->mediumText('joining_benefits')->after('joining_fee')->nullable();
            $table->mediumText('website_detail')->after('joining_benefits')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('joining_fee');
            $table->dropColumn('joining_benefits');
            $table->dropColumn('website_detail');
        });
    }
}
