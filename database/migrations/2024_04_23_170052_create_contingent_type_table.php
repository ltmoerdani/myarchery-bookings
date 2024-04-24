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
        Schema::create('contingent_type', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->string('contingent_type');
            $table->string('select_type')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('country')->nullable();
            $table->integer('province_id')->nullable();
            $table->string('province')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('state')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city')->nullable();
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
        Schema::dropIfExists('contingent_type');
    }
};
