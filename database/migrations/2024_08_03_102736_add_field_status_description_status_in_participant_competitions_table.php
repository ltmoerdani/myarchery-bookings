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
            $table->integer('status')->comment('1 = active; 2 = cancel; 3 = refund;')->after('description')->default(1);
            $table->string('description_status')->nullable()->after('status');
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
            $table->dropColumn('status');
            $table->dropColumn('description_status');
        });
    }
};
