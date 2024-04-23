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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('event_id');
            $table->integer('competition_type_id');
            $table->integer('competition_category_id');
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('contingent')->nullable();
            $table->string('distance')->nullable();
            $table->string('class_type')->nullable();
            $table->string('class_name')->nullable();
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
        Schema::dropIfExists('competitions');
    }
};
