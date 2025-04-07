<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWorkingShiftTableBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //working_shifts
        Schema::table('working_shifts', function (Blueprint $table) {
            //
            // Foreign key column that can be nullable
            $table->enum('base', ['4','5', '6','7','8','9','10','11','12'])->default('8');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
