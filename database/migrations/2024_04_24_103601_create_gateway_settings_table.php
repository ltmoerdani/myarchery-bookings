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
        Schema::create('gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->integer('currency_id');
            $table->string('gateway_type');
            $table->string('gateway_id')->comment('ref from online or offline gateway id');
            $table->boolean('percentage_status')->default(0);
            $table->decimal('charge', total: 8, places: 2);
            $table->decimal('fee', total: 8, places: 2);
            $table->integer('min_limit')->nullable();
            $table->integer('max_limit')->nullable();
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('gateway_settings');
    }
};
