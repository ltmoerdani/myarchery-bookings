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
        Schema::create('indonesian_cities', function (Blueprint $table) {
            $table->id();
            $table->string('id_area');
            $table->string('kode');
            $table->foreignId('province_id')->constrained('indonesian_province')->references('id')->onDelete('cascade');
            $table->string('name');
            $table->string('level')->comment('1=province, 2=city, 3=subdistrict, 4=district');
            $table->longText('timezones')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        Schema::dropIfExists('indonesian_cities');
    }
};
