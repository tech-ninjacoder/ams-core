<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelmetUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helmet_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained();
            $table->foreignId('helmet_id')->constrained();
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
        Schema::dropIfExists('helmet_user');
    }
}
