<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAccountTypeIdOnBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->foreignId('account_type_id')
                ->after('account_name')
                ->nullable()
                ->constrained('bank_account_types')
                ->nullOnDelete();

            $table->dropColumn('account_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_type_id');

            $table->enum('account_type', ['chequing', 'savings']);
        });
    }
}
