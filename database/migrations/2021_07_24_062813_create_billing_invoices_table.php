<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Platform\Models\Plan;

class CreateBillingInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('order_id')->unique();

            $table->unsignedInteger('plan_id')->nullable();

            $table->string('payment_method');

            $table->foreignId('bank_id')
                ->nullable()
                ->constrained('bank_details')
                ->nullOnDelete();

            $table->decimal('amount', 16, 2)->default(0.00);

            $table->string('merchant_bank_name')->nullable();

            $table->string('merchant_identifier')->nullable();

            $table->decimal('amount_paid', 16, 2)->default(0.00);

            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');

            $table->date('paid_at')->nullable();

            $table->timestamp('expired_at');

            $table->foreign('plan_id')
                ->on('plans')
                ->references('id')
                ->nullOnDelete();

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
        Schema::dropIfExists('billing_invoices');
    }
}
