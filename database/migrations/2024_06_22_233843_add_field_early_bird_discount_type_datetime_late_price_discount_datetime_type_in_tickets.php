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
      $table->string('early_bird_discount_amount')->nullable()->comment('use for pricing local earlybird tournament and early bird pricing outer tournament event')->change();
      $table->string('early_bird_discount_type')->nullable()->comment('use for pricing local earlybird tournament and early bird pricing outer tournament event')->change();
      $table->string('early_bird_discount_date')->nullable()->comment('use for start date local earlybird tournament and early bird date outer tournament event')->change();
      $table->string('early_bird_discount_time')->nullable()->comment('use for start time local earlybird tournament and early bird time outer tournament event')->change();
      $table->string('early_bird_discount_end_date')->nullable()->comment('user for local end date early bird tournament and support use outer tournament event')->after('early_bird_discount_time')->change();
      $table->string('early_bird_discount_end_time')->nullable()->comment('user for local end time early bird tournament and support use outer tournament event')->after('early_bird_discount_end_date');
      $table->string('early_bird_discount_amount_international')->nullable()->after('early_bird_discount_end_time');
      // $table->string('early_bird_discount_amount_international')->nullable()->after('early_bird_discount_end_time')->change();
      $table->string('early_bird_discount_international_type')->nullable()->comment('use for type international earlybird tournament')->after('early_bird_discount_amount_international');
      $table->string('early_bird_discount_international_date')->nullable()->comment('use for start date international earlybird tournament and support if use outer tournament event')->after('early_bird_discount_international_type');
      $table->string('early_bird_discount_international_time')->nullable()->comment('use for start time international earlybird tournament and support if use outer tournament event')->after('early_bird_discount_international_date');
      $table->string('early_bird_discount_international_end_date')->nullable()->comment('use for end date international earlybird tournament and support if use outer tournament event')->after('early_bird_discount_international_time');
      $table->string('early_bird_discount_international_end_time')->nullable()->comment('use for end time international earlybird tournament and support if use outer tournament event')->after('early_bird_discount_international_end_date');
      $table->string('late_price_discount_amount')->nullable()->comment('use for local late price tournament and support for outer tournament event')->change();
      $table->string('late_price_discount_type')->nullable()->after('late_price_discount_amount')->comment('use for local late price tournament and support for outer tournament event')->change();
      $table->string('late_price_discount_date')->nullable()->after('late_price_discount_type')->comment('use for local start date late price tournament and support for outer tournament event')->change();
      $table->string('late_price_discount_time')->nullable()->after('late_price_discount_date')->comment('use for local start time late price tournament and support for outer tournament event')->change();
      $table->string('late_price_discount_end_date')->nullable()->after('late_price_discount_time')->comment('use for local end date late price tournament and support for outer tournament event');
      $table->string('late_price_discount_end_time')->nullable()->after('late_price_discount_end_date')->comment('use for local end time late price tournament and support for outer tournament event');
      $table->string('late_price_discount_amount_international')->nullable()->after('late_price_discount_end_time')->comment('use for international late price tournament and support for outer tournament event')->change();
      $table->string('late_price_discount_international_type')->nullable()->after('late_price_discount_amount_international')->comment('use for international late price tournament and support for outer tournament event');
      $table->string('late_price_discount_international_date')->nullable()->after('late_price_discount_international_type')->comment('use for start date international late price tournament and support for outer tournament event');
      $table->string('late_price_discount_international_time')->nullable()->after('late_price_discount_international_date')->comment('use for start time international late price tournament and support for outer tournament event');
      $table->string('late_price_discount_international_end_date')->nullable()->after('late_price_discount_international_time')->comment('use for end date international late price tournament and support for outer tournament event');
      $table->string('late_price_discount_international_end_time')->nullable()->after('late_price_discount_international_end_date')->comment('use for end time international late price tournament and support for outer tournament event');
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
      // change column
      $table->string('early_bird_discount_amount')->nullable()->after('early_bird_discount')->change();
      $table->string('early_bird_discount_amount_international')->nullable()->after('early_bird_discount_amount')->change();
      $table->string('early_bird_discount_type')->nullable()->after('early_bird_discount_amount_international')->change();
      $table->string('early_bird_discount_date')->nullable()->after('early_bird_discount_type')->change();
      $table->string('early_bird_discount_time')->nullable()->after('early_bird_discount_date')->change();
      $table->string('late_price_discount_amount')->nullable()->after('late_price_discount')->change();
      $table->string('late_price_discount_amount_international')->nullable()->after('late_price_discount_amount')->change();
      $table->string('late_price_discount_type')->nullable()->after('late_price_discount_amount_international')->change();
      $table->string('late_price_discount_date')->nullable()->after('late_price_discount_type')->change();
      $table->string('late_price_discount_time')->nullable()->after('late_price_discount_date')->change();

      // drop column
      $table->dropColumn('early_bird_discount_end_time');
      $table->dropColumn('early_bird_discount_amount_international');
      $table->dropColumn('early_bird_discount_international_type');
      $table->dropColumn('early_bird_discount_international_date');
      $table->dropColumn('early_bird_discount_international_time');
      $table->dropColumn('early_bird_discount_international_end_date');
      $table->dropColumn('early_bird_discount_international_end_time');
      $table->dropColumn('late_price_discount_international_type');
      $table->dropColumn('late_price_discount_international_date');
      $table->dropColumn('late_price_discount_international_time');
      $table->dropColumn('late_price_discount_international_end_date');
      $table->dropColumn('late_price_discount_international_end_time');
    });
  }
};
