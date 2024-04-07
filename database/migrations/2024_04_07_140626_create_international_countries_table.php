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
      );
      $table->string(
        'iso2'
      );
      $table->integer('numeric_code');
      $table->string(
        'phone_code'
      );
      $table->string(
        'capital'
      );
      $table->string(
        'currency'
      );
      $table->string(
        'currency_name'
      );
      $table->string(
        'currency_symbol'
      );
      $table->string(
        'tld'
      );
      $table->string(
        'native'
      );
      $table->string(
        'region'
      );
      $table->integer(
        'region_id'
      );
      $table->string(
        'sub_region'
      );
      $table->integer(
        'sub_region_id'
      );
      $table->string(
        'nationality'
      );
      $table->longText(
        'timezones'
      );
      $table->string(
        'latitude'
      );
      $table->string(
        'longitude'
      );
      $table->string(
        'emoji'
      );
      $table->string(
        'emojiU'
      );
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
