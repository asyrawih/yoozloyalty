<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('blueprint');
            $table->string('country_isd_code');
            $table->boolean('is_active')->default(0);
            $table->json('schema')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_services');
    }
}
