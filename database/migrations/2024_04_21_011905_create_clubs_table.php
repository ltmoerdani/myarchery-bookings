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
    Schema::create('clubs', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('logo')->nullable();
      $table->text(
        'banner'
      )->nullable();
      $table->string(
        'place_name'
      )->nullable();
      $table
        ->foreignId('country_id')
        ->nullable()
        ->constrained('international_countries')
        ->references('id')
        ->onDelete('cascade');
      $table->string(
        'country_name'
      )->nullable();
      $table->bigInteger('state_id')->nullable();
      $table->string(
        'state_name'
      )->nullable();
      $table->bigInteger('city_id')->nullable();
      $table->string(
        'city_name'
      )->nullable();
      $table->text(
        'address'
      )->nullable();
      $table->text(
        'description'
      )->nullable();
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
    Schema::dropIfExists('clubs');
  }
};
