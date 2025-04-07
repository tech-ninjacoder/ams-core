<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectGatePassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_gate_passes', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained();
            $table->foreignId('gate_passe_id')->constrained();
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
        Schema::dropIfExists('project_gate_passes');
    }
}
