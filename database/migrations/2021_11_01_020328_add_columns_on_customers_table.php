<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['street1', 'street2', 'state']);

            $table->string('street_name')
                ->after('gender')
                ->nullable();

            $table->string('country')
                ->after('city')
                ->nullable();

            $table->unsignedTinyInteger('card_status')
                ->after('card_number')
                ->default(1);

            DB::statement("ALTER TABLE customers MODIFY COLUMN gender ENUM('M', 'F') NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['country', 'card_status']);

            $table->string('street1')->after('fax')->nullable();

            $table->string('street2')->after('street1')->nullable();

            $table->string('state', 64)->after('city')->nullable();

            $table->unsignedSmallInteger('gender')->nullable()->change();
        });
    }
}
