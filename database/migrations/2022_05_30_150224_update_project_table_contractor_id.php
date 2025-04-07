<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectTableContractorId extends Migration
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
            $table->foreignId('contractor_id')->nullable()->after('lunch_in')->constrained();

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
            $table->dropColumn('contractor_id');
        });

    }
}
