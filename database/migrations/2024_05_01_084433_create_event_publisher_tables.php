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
        Schema::create('event_publisher', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->integer('event_id');
            $table->string('shared_type')->nullable();
            $table->string('link_event')->nullable();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_publisher');
    }
};
