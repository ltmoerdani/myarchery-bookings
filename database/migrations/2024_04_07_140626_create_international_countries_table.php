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
    Schema::create('international_countries', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string(
        'iso3'
      )->nullable();
      $table->string(
        'iso2'
      )->nullable();
      $table->string('numeric_code')->nullable();
      $table->string(
        'phone_code'
      )->nullable();
      $table->string(
        'capital'
      )->nullable();
      $table->string(
        'currency'
      )->nullable();
      $table->string(
        'currency_name'
      )->nullable();
      $table->string(
        'currency_symbol'
      )->nullable();
      $table->string(
        'tld'
      )->nullable();
      $table->string(
        'native'
      )->nullable();
      $table->string(
        'region'
      )->nullable();
      $table->string(
        'region_id'
      )->nullable();
      $table->string(
        'sub_region'
      )->nullable();
      $table->string(
        'sub_region_id'
      )->nullable();
      $table->string(
        'nationality'
      )->nullable();
      $table->longText(
        'timezones'
      )->nullable();
      $table->string(
        'latitude'
      )->nullable();
      $table->string(
        'longitude'
      )->nullable();
      $table->string(
        'emoji'
      )->nullable();
      $table->string(
        'emojiU'
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
    Schema::dropIfExists('international_countries');
  }
};
