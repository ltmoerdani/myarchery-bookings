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
    Schema::create('customer_club_members', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('customer_id')
        ->constrained('customers')
        ->references('id')
        ->onDelete('cascade');
      $table
        ->foreignId('club_id')
        ->constrained('clubs')
        ->references('id')
        ->onDelete('cascade');
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
    Schema::dropIfExists('customer_club_members');
  }
};
