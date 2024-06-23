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
        Schema::table('gateway_settings', function (Blueprint $table) {
            $table->dropColumn('currency_id');
            $table->dropColumn('gateway_id');
            $table->dropColumn('percentage_status');
            $table->dropColumn('charge');
            $table->string('currency')->nullable()->after('gateway_type');
            $table->string('payment_channel')->nullable()->after('currency');
            $table->string('percentage_amount')->nullable()->after('payment_channel');
            $table->string('fixed_amount')->nullable()->after('percentage_amount');
            $table->string('min_limit', 255)->nullable()->change();
            $table->string('max_limit', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_settings', function (Blueprint $table) {
            $table->string('currency_id')->nullable()->after('gateway_type');
            $table->string('gateway_id')->nullable()->after('currency_id');
            $table->string('percentage_status')->nullable()->after('gateway_id');
            $table->string('charge')->nullable()->after('percentage_status');
            $table->dropColumn('currency');
            $table->dropColumn('payment_channel');
            $table->dropColumn('percentage_amount');
            $table->dropColumn('fixed_amount');
            $table->integer('min_limit', 255)->nullable()->change();
            $table->integer('max_limit', 255)->nullable()->change();
        });
    }
};
