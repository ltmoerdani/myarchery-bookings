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
    Schema::create('international_states', function (Blueprint $table) {
      $table->id();
      $table->string(
        'name'
      );
      $table
        ->foreignId('country_id')
        ->constrained('international_countries')
        ->references('id')
        ->onDelete('cascade');
      $table->string(
        'country_code'
      );
      $table->string(
        'country_name'
      );
      $table->string(
        'state_code'
      )->nullable();
      $table->string(
        'type'
      )->nullable();
      $table->string(
        'latitude'
      )->nullable();
      $table->string(
        'longitude'
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
    Schema::dropIfExists('international_states');
  }
};
