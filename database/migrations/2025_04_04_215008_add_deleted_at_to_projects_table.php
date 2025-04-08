<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('projects', 'deleted_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->softDeletes(); // adds `deleted_at` column
        }   );
        }
       
    }

    public function down()
    {
        if (Schema::hasColumn('projects', 'deleted_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
       
    }

};
