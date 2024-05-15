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
        Schema::create('participant', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('lname')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('birthdate')->nullable();
            $table->integer('county_id')->nullable();
            $table->string('country')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city')->nullable();
            $table->string('category')->nullable();
            $table->integer('club_id')->nullable();
            $table->integer('customer_id')->nullable();
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
        Schema::dropIfExists('participant');
    }
};
