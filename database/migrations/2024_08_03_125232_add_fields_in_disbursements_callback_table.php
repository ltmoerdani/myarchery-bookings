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
        Schema::table('disbursements_callback', function (Blueprint $table) {
            $table->string('user_id')->nullable()->after('external_id');
            $table->string('account_number')->nullable()->after('bank_code');
            $table->string('is_instant')->nullable()->after('created_callback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disbursements_callback', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('account_number');
            $table->dropColumn('is_instant');
        });
    }
};
