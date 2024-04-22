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
    Schema::table('customers', function (Blueprint $table) {
      $table->bigInteger('country_id')->nullable()->after('address');
      $table->bigInteger('state_id')->nullable()->after('country');
      $table->bigInteger('city_id')->nullable()->after('state');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('customers', function (Blueprint $table) {
      $table->dropColumn('country_id');
      $table->dropColumn(
        'state_id'
      );
      $table->dropColumn('city_id');
    });
  }
};
