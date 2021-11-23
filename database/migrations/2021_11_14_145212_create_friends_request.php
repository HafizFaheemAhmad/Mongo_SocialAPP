<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendsRequest extends Migration
{

    public function up()
    {
        Schema::create('friends_request', function (Blueprint $table) {
            $table->id('f_id');
            $table->bigInteger('userid_1')->unsigned();
            $table->bigInteger('userid_2')->unsigned();
            $table->timestamps();
            $table->foreign('userid_1')->references('id')->on('users');
            $table->foreign('userid_2')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('friends_request');
    }
}
