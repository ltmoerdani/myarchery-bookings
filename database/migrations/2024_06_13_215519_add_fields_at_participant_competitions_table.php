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
            $table->integer('event_id')->after('competition_name')->nullable();
            $table->string('category')->after('booking_id')->nullable();
            $table->integer('delegation_id')->after('category')->nullable();
            $table->integer('customer_id')->after('delegation_id')->nullable();
        });

        DB::statement('UPDATE `participant` p, participant_competitions pc SET pc.category = p.category, pc.delegation_id = p.delegation_id, pc.customer_id = p.customer_id WHERE p.id=pc.participant_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant_competitions', function (Blueprint $table) {
            $table->dropColumn('event_id');
            $table->dropColumn('category');
            $table->dropColumn('delegation_id');
            $table->dropColumn('customer_id');
        });
    }
};
