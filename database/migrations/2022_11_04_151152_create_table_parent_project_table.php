<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableParentProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->references('id')->on('projects');
            $table->foreignId('parent_id')->references('id')->on('projects');
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
        Schema::dropIfExists('table_parent_project');
    }
}
