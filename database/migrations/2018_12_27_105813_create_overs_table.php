<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('match_id')->unsigned()->nullable();
            $table->foreign('match_id')->references('id')->on('matches');
            $table->integer('bowler_id')->unsigned()->nullable();
            $table->foreign('bowler_id')->references('id')->on('players');
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
        Schema::dropIfExists('overs');
    }
}
