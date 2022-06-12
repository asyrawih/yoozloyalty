<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSmtpServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smtp_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('smtp_name');

            $table->string('mail_from_name');

            $table->string('mail_from_address');

            $table->string('mail_driver')->default('smtp');

            $table->string('mail_host');

            $table->string('mail_port');

            $table->string('mail_username');

            $table->string('mail_password');

            $table->string('mail_encryption')->default('tls');

            $table->boolean('is_active')->default(FALSE);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE `smtp_services` ADD `uuid` BINARY(16) UNIQUE NULL AFTER `id`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smtp_services');
    }
}
