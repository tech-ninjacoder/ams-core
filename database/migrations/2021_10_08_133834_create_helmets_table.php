<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelmetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helmets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('imei');
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained();
            $table->boolean('is_default')->default(0);
            $table->unsignedBigInteger('tenant_id');
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
        Schema::dropIfExists('helmets');
    }
}
