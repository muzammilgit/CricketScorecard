<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('match_date')->useCurrent();
            $table->string('no_of_overs');
            $table->integer('toss_won_by')->unsigned()->nullable();
            $table->foreign('toss_won_by')->references('id')->on('teams');
            $table->string('elected_to');
            $table->integer('won_by')->nullable();
            $table->integer('team_a_id')->unsigned()->nullable();
            $table->integer('team_b_id')->unsigned()->nullable();
            $table->foreign('team_a_id')->references('id')->on('teams');
            $table->foreign('team_b_id')->references('id')->on('teams');
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
        Schema::dropIfExists('matches');
    }
}
