<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUseTableGuardianId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            // Add the guardian_id foreign key, which can be null
            $table->unsignedBigInteger('guardian_id')->nullable()->after('is_guardian');

            // Optionally, add a foreign key constraint if the guardian is another user in the same table
            $table->foreign('guardian_id')->references('id')->on('users')->onDelete('set null');
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
    }
}
