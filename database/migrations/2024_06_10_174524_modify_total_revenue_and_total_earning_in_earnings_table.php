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
        DB::statement('ALTER TABLE earnings MODIFY COLUMN `total_revenue` FLOAT(20,2) DEFAULT 0');
        DB::statement('ALTER TABLE earnings MODIFY COLUMN `total_earning` DOUBLE(20,2) DEFAULT 0');

        // Schema::table('earnings', function (Blueprint $table) {
        //     $table->float('total_revenue', 12, 2)->nullable()->change();
        //     $table->double('total_earning', 12, 2)->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE earnings MODIFY COLUMN `total_revenue` FLOAT(8,2) DEFAULT 0');
        DB::statement('ALTER TABLE earnings MODIFY COLUMN `total_earning` DOUBLE(8,2) DEFAULT 0');

        // Schema::table('earnings', function (Blueprint $table) {
        //     $table->float('total_revenue', 8, 2)->nullable()->change();
        //     $table->double('total_earning', 8, 2)->nullable()->change();
        // });
    }
};
