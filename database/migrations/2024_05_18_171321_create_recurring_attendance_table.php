<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('working_shift_id')->constrained();
            $table->time('in_time');
            $table->time('out_time');
            $table->foreignId('added_by')->nullable()->constrained('users', 'id');
            $table->foreignId('project_id')->nullable()->constrained('projects', 'id');
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
        Schema::dropIfExists('recurring_attendance');
    }
}
