<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGatePassesTableValidity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
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
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn('valid_from');
            $table->dropColumn('valid_to');

        });
    }
}
