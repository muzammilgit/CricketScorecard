<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('isvalid');
            $table->integer('out_param')->nullable();
            $table->integer('fielder_1')->unsigned()->nullable();
            $table->foreign('fielder_1')->references('id')->on('players');
            $table->integer('fielder_2')->unsigned()->nullable();
            $table->foreign('fielder_2')->references('id')->on('players');
            $table->string('runs_scored');
            $table->integer('scored_by')->unsigned()->nullable();
            $table->foreign('scored_by')->references('id')->on('players');
            $table->integer('over_id')->unsigned()->nullable();
            $table->foreign('over_id')->references('id')->on('overs');
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
        Schema::dropIfExists('balls');
    }
}
