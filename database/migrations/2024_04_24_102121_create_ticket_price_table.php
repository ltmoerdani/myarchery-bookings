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
        Schema::create('ticket_price', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id');
            $table->integer('currency_id');
            $table->string('title');
            $table->string('ticket_available_type')->nullable();
            $table->integer('ticket_available')->nullable();
            $table->string('max_ticket_buy_type')->nullable();
            $table->integer('max_buy_ticket')->nullable();
            $table->string('pricing_type')->nullable();
            $table->decimal('price', total: 8, places: 2);
            $table->decimal('f_price', total: 8, places: 2);
            $table->string('early_bird_discount')->nullable();
            $table->string('early_bird_discount_amount')->nullable();
            $table->string('early_bird_discount_type')->nullable();
            $table->string('early_bird_discount_date')->nullable();
            $table->string('early_bird_discount_time')->nullable();
            $table->longText('variations')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('ticket_price');
    }
};
