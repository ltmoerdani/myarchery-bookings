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
        Schema::table('participant', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('delegation_id');
            $table->dropColumn('customer_id');
            $table->string('username')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant', function (Blueprint $table) {
            $table->string('category')->after('booking_id')->nullable();
            $table->integer('delegation_id')->after('category')->nullable();
            $table->dropColumn('username');
            $table->integer('customer_id')->after('delegation_id')->nullable();
        });
    }
};
