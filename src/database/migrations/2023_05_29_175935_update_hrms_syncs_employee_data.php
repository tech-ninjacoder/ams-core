<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHrmsSyncsEmployeeData extends Migration
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
            $table->string('ProjectID')->after('attendance_date')->nullable();
            $table->string('Morning')->after('ProjectID')->nullable();
            $table->string('Afternoon')->after('Morning')->nullable();
            $table->string('TotalHours')->after('Afternoon')->nullable();
            $table->string('IncentiveHours')->after('TotalHours')->nullable();
            $table->string('OffDayOTHours')->after('IncentiveHours')->nullable();
            $table->string('HolidayOTHours')->after('OffDayOTHours')->nullable();
            $table->string('OffDayOrHoliday')->after('HolidayOTHours')->nullable();
            $table->string('Remarks')->after('OffDayOrHoliday')->nullable();
            $table->string('ExportUser')->after('Remarks')->nullable();


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
