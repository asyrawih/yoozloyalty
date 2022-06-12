<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnCreditRuleConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('credit_rule_conversions', 'rule')) {
            Schema::table('credit_rule_conversions', function (Blueprint $table) {
                $table->dropColumn('rule');
            });
        }

        if (Schema::hasColumn('credit_rule_conversions', 'value')) {
            Schema::table('credit_rule_conversions', function (Blueprint $table) {
                $table->dropColumn('value');
            });
        }

        Schema::table('credit_rule_conversions', function (Blueprint $table) {
            $table->unsignedBigInteger('min_amount')
                ->default(0)
                ->change();

            $table->unsignedBigInteger('max_amount')
                ->default(0)
                ->change();

            $table->unsignedBigInteger('rate')
                ->default(0)
                ->after('max_amount');

            $table->enum('type', ['F', 'P'])
                ->default('F')
                ->comment('F = Fixed, P = Percent')
                ->after('rate');

            $table->boolean('stepping_mode')
                ->default(0)
                ->after('type');

            $table->unsignedBigInteger('step_amount')
                ->default(0)
                ->after('stepping_mode');

            $table->enum('mode', ['range', 'step'])
                ->default('range')
                ->after('step_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_rule_conversions', function (Blueprint $table) {
            $table->dropColumn([
                'stepping_mode',
                'step_amount',
                'mode',
                'rate',
                'type',
            ]);

            $table->unsignedBigInteger('value')
                ->default(0)
                ->after('max_amount');

            $table->enum('rule', ['fixed', 'percent'])
                ->default('fixed')
                ->after('value');
        });
    }
}
