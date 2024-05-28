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
    Schema::table('tickets', function (Blueprint $table) {
      $table->dropColumn('international_price');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('tickets', function (Blueprint $table) {
      $table->double('international_price')->nullable()->after('f_price');
    });
  }
};
