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
    Schema::create('international_cities', function (Blueprint $table) {
      $table->id();
      $table->string(
        'name'
      );
      $table->foreignId('state_id')
        ->constrained('international_states')
        ->references('id')
        ->onDelete('cascade');
      $table->string(
        'state_code'
      );
      $table->string(
        'state_name'
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
        'latitude'
      )->nullable();
      $table->string(
        'longitude'
      )->nullable();
      $table->string(
        'wikiDataId'
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
    Schema::dropIfExists('international_cities');
  }
};
