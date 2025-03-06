<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('department_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('transfer_request_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_request_id');
            $table->string('comment');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('transfer_request_comments', function (Blueprint $table) {
            $table->foreign('transfer_request_id')->references('id')->on('transfer_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_request_comments');
        Schema::dropIfExists('transfer_requests');
    }
}
