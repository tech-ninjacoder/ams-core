<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE gate_pass_user
            MODIFY start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       DB::statement("
            ALTER TABLE gate_pass_user
            MODIFY start_date TIMESTAMP NULL DEFAULT NULL
        ");
    }
};
