<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringAttendanceUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recur_at_user', function (Blueprint $table) {
            $table->foreignId('recurring_attendance_id')->constrained()->references('id')->on('recurring_attendances')->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->date('start_date');
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurring_attendance_user');
    }
}
