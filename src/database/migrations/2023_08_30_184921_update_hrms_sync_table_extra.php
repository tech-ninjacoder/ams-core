<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHrmsSyncTableExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('hrms_syncs', function (Blueprint $table) {
            //
            // Foreign key column that can be nullable
            $table->decimal('NormalOTHours')->nullable();
            $table->decimal('ExtraOTHours')->nullable();
            $table->decimal('BaseWorkShiftHours')->nullable();

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
