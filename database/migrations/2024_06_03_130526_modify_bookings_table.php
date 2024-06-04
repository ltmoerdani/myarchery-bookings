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
    Schema::table('bookings', function (Blueprint $table) {
      $table->string('price', 255)->nullable()->change();
      $table->string('discount', 255)->nullable()->change();
      $table->string('tax', 255)->nullable()->change();
      $table->string('commission', 255)->nullable()->change();
      $table->string('early_bird_discount', 255)->nullable()->change();
      $table->string('late_price_discount', 255)->nullable()->after('early_bird_discount');
      $table->string('tax_percentage', 255)->nullable()->change();
      $table->string('commission_percentage', 255)->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('bookings', function (Blueprint $table) {
      $table->double('price')->nullable()->change();
      $table->double('discount')->nullable()->change();
      $table->double('tax')->nullable()->change();
      $table->double('commission')->nullable()->change();
      $table->double('early_bird_discount')->nullable()->change();
      $table->dropColumn('late_price_discount');
      $table->double('tax_percentage')->nullable()->change();
      $table->double('commission_percentage')->nullable()->change();
    });
  }
};
