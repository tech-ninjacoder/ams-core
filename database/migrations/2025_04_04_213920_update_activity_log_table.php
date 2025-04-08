<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
        if (!Schema::hasColumn('activity_log', 'batch_uuid')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->uuid('batch_uuid')->nullable()->after('causer_type')->index();
                $table->string('event')->nullable()->after('batch_uuid')->index();
            });
        }
    }

    public function down(){
        if (Schema::hasColumn('activity_log', 'batch_uuid')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropColumn('batch_uuid');
                $table->dropColumn('event');
            });
        }
    }

};
