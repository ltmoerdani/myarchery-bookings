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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('withdraw_id');
            $table->string('payment_type');
            $table->longText('callback')->nullable();
            $table->longText('req_header')->nullable();
            $table->string('callback_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('disbursement_description')->nullable();
            $table->string('status')->nullable();
            $table->string('currency')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('disbursements');
    }
};
