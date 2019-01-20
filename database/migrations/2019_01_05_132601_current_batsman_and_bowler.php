<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CurrentBatsmanAndBowler extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_batsman_and_bowler', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->integer('b1_id')->unsigned()->nullable();
            $table->foreign('b1_id')->references('id')->on('players');
            $table->integer('b2_id')->unsigned()->nullable();
            $table->foreign('b2_id')->references('id')->on('players');
            $table->integer('on_strike')->unsigned()->nullable();
            $table->foreign('on_strike')->references('id')->on('players');
            $table->integer('bowler_id')->unsigned()->nullable();
            $table->foreign('bowler_id')->references('id')->on('players');
            $table->integer('m_id')->unsigned()->nullable();
            $table->foreign('m_id')->references('id')->on('matches');
            $table->integer('is_delete')->nullable();
            $table->integer('innings')->nullable();
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
        Schema::table('current_batsman_and_bowler', function (Blueprint $table) {
            //
        });
    }
}
