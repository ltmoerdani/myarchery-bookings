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
    Schema::table('transactions', function (Blueprint $table) {
      $table->string('grand_total', 255)->nullable()->change();
      $table->string('commission', 255)->nullable()->default(0.00)->change();
      $table->string('tax', 255)->nullable()->default(0.00)->change();
      $table->string('pre_balance', 255)->nullable()->default(0.00)->change();
      $table->string('after_balance', 255)->nullable()->default(0.00)->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->double('grand_total')->nullable()->change();
      $table->float('commission')->nullable()->default(0.00)->change();
      $table->float('tax')->nullable()->default(0.00)->change();
      $table->float('pre_balance')->nullable()->default(0.00)->change();
      $table->float('after_balance')->nullable()->default(0.00)->change();
    });
  }
};
