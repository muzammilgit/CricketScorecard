<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Playing11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('playing11', function (Blueprint $table) {
           $table->increments('id');
           $table->integer('player_id')->unsigned()->nullable();
           $table->foreign('player_id')->references('id')->on('players');
           $table->integer('match_id')->unsigned()->nullable();
           $table->foreign('match_id')->references('id')->on('matches');
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
        //
        Schema::dropIfExists('playing11');
    }
}
