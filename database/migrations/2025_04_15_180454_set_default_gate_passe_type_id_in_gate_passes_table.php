<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            // Step 1: Drop the foreign key
            $table->dropForeign(['gate_passe_type_id']);

            // Step 2: Alter the column (example: integer with default 1, not null)
            $table->unsignedBigInteger('gate_passe_type_id')->default(1)->change();

            // Step 3: Re-add the foreign key
            $table->foreign('gate_passe_type_id')->references('id')->on('gate_passe_types');
        });

        $exists = DB::table('gate_passe_types')
        ->where('id', 1)
        ->exists();

        if (!$exists) {
            DB::table('gate_passe_types')->insert([
                'id' => 1,
                'name' => 'Default Pass',
                'description' => 'Default Pass Type',
                'created_at' => now(),
                'updated_at' => now()
            ]);  // values to insert
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropForeign(['gate_passe_type_id']);
            $table->unsignedBigInteger('gate_passe_type_id')->nullable()->change();
            $table->foreign('gate_passe_type_id')->references('id')->on('gate_passe_types');
        });
    }
};
