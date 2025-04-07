<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsTable01032022 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('projects', function (Blueprint $table) {
            $table->date('p_start_date')->nullable()->after('location');
            $table->date('p_end_date')->nullable()->after('p_start_date');
            $table->integer('est_man_hour')->nullable()->after('p_end_date');
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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('p_start_date');
            $table->dropColumn('p_end_date');
            $table->dropColumn('est_man_hour');
        });

    }
}
