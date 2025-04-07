<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordDefaultUser extends Migration
{
    public function up()
    {
        DB::table('users')
            ->where('email', 'admin@demo.com')
            ->update([ 'password' => Hash::make('LetmeinDB@2025!')]);
    }

    public function down()
    {
        // do nothing, no need to revert back to the original user
    }
}
?>
