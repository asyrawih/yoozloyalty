<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeHoursToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->after('meta', function($table){
                $table->integer('timezone_id')->unsigned()->default(1);
                $table->boolean('active_monday')->default(false);
                $table->boolean('active_tuesday')->default(false);
                $table->boolean('active_wednesday')->default(false);
                $table->boolean('active_thursday')->default(false);
                $table->boolean('active_friday')->default(false);
                $table->boolean('active_saturday')->default(false);
                $table->boolean('active_sunday')->default(false);
                $table->string('active_monday_from', 5)->nullable();
                $table->string('active_tuesday_from', 5)->nullable();
                $table->string('active_wednesday_from', 5)->nullable();
                $table->string('active_thursday_from', 5)->nullable();
                $table->string('active_friday_from', 5)->nullable();
                $table->string('active_saturday_from', 5)->nullable();
                $table->string('active_sunday_from', 5)->nullable();
                $table->string('active_monday_to', 5)->nullable();
                $table->string('active_tuesday_to', 5)->nullable();
                $table->string('active_wednesday_to', 5)->nullable();
                $table->string('active_thursday_to', 5)->nullable();
                $table->string('active_friday_to', 5)->nullable();
                $table->string('active_saturday_to', 5)->nullable();
                $table->string('active_sunday_to', 5)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
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
