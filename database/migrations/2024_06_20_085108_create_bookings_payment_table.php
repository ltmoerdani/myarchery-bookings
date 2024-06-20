<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings_payment', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id');
            $table->string('payment_type');
            $table->longText('callback')->nullable();
            $table->string('external_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->string('amount')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('paid_at')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('description')->nullable();
            $table->string('adjusted_received_amount')->nullable();
            $table->string('fees_paid_amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_destination')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('bookings_payment');
    }
};
