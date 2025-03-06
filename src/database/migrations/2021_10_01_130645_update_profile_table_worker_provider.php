<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileTableWorkerProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('profiles', function (Blueprint $table) {
           //table->foreignId('work_provider_id')->after('employee_id')->nullable()->constrained();
            $table->unsignedBigInteger('work_provider_id')->nullable();
            $table->foreign('work_provider_id')->references('id')->on('workers_providers');

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
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['work_provider_id']);
            $table->dropColumn('work_provider_id');

        });
    }
}
