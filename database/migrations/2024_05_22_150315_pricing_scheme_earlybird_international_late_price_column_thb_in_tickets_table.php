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
      $table->string('pricing_scheme', '255')->default('single_price')->after('description');
      $table->string('early_bird_discount_amount_international', '255')->nullable()->after('early_bird_discount_amount');
      $table->string('late_price_discount', '255')->default('disable')->after('early_bird_discount_time');
      $table->string('late_price_discount_amount', '255')->nullable()->after('late_price_discount');
      $table->string('late_price_discount_amount_international', '255')->nullable()->after('late_price_discount_amount');
      $table->string('late_price_discount_date', '255')->nullable()->after('late_price_discount_amount_international');
      $table->string('late_price_discount_time', '255')->nullable()->after('late_price_discount_date');
      $table->string('late_price_discount_type', '255')->nullable()->after('late_price_discount_time');
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
      $table->dropColumn('pricing_scheme');
      $table->dropColumn('early_bird_discount_amount_international');
      $table->dropColumn('late_price_discount');
      $table->dropColumn('late_price_discount_amount');
      $table->dropColumn('late_price_discount_date');
      $table->dropColumn('late_price_discount_time');
      $table->dropColumn('late_price_discount_type');
      $table->dropColumn('late_price_discount_amount_international');
    });
  }
};
