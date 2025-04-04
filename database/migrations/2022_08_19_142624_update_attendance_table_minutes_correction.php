<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceTableMinutesCorrection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('minutes_correction')->after('hours_correction')->default(0);
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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('minutes_correction');
        });
    }
}
