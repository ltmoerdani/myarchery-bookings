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
    Schema::table('participant_competitions', function (Blueprint $table) {
      $table->bigInteger('country_id')->nullable()->after('delegation_id')->comment('use for mapping data from international or national region');
      $table->bigInteger('province_id')->nullable()->after('country_id')->comment('use for mapping data from international or national region');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('participant_competitions', function (Blueprint $table) {
      $table->dropColumn('country_id');
      $table->dropColumn('province_id');
    });
  }
};
