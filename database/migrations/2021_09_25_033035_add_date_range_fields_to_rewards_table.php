<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeFieldsToRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('active_sunday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_saturday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_friday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_thursday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_wednesday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_tuesday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_monday_to', 5)->nullable()->default('23:59')->after('multiple_time');
            $table->string('active_sunday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_saturday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_friday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_thursday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_wednesday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_tuesday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->string('active_monday_from', 5)->nullable()->default('00:00')->after('multiple_time');
            $table->boolean('active_sunday')->default(false)->after('multiple_time');
            $table->boolean('active_saturday')->default(false)->after('multiple_time');
            $table->boolean('active_friday')->default(false)->after('multiple_time');
            $table->boolean('active_thursday')->default(false)->after('multiple_time');
            $table->boolean('active_wednesday')->default(false)->after('multiple_time');
            $table->boolean('active_tuesday')->default(false)->after('multiple_time');
            $table->boolean('active_monday')->default(false)->after('multiple_time');
            $table->dateTime('expires_at')->nullable()->after('multiple_time');
            $table->dateTime('active_from')->nullable()->after('multiple_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('active_from');
            $table->dropColumn('expires_at');
            $table->dropColumn('active_monday');
            $table->dropColumn('active_tuesday');
            $table->dropColumn('active_wednesday');
            $table->dropColumn('active_thursday');
            $table->dropColumn('active_friday');
            $table->dropColumn('active_saturday');
            $table->dropColumn('active_sunday');
            $table->dropColumn('active_monday_from');
            $table->dropColumn('active_tuesday_from');
            $table->dropColumn('active_wednesday_from');
            $table->dropColumn('active_thursday_from');
            $table->dropColumn('active_friday_from');
            $table->dropColumn('active_saturday_from');
            $table->dropColumn('active_sunday_from');
            $table->dropColumn('active_monday_to');
            $table->dropColumn('active_tuesday_to');
            $table->dropColumn('active_wednesday_to');
            $table->dropColumn('active_thursday_to');
            $table->dropColumn('active_friday_to');
            $table->dropColumn('active_saturday_to');
            $table->dropColumn('active_sunday_to');
        });
    }
}
