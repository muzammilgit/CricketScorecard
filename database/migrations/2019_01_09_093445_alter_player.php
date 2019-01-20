<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('players', function (Blueprint $table) {
            $table->integer('is_delete')->nullable();
            $table->integer('player_param')->nullable()->comment('1 => batsman,2 => bowler,3 => allRounder');
            $table->integer('enable_voting')->nullable()->comment('1 => enable, NULL => not enabled');
            $table->integer('is_playing')->nullable()->comment('1 => playing,NULL => not playing');
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
        Schema::table('players', function (Blueprint $table) {
           $table->dropColumn('player_param');
           $table->dropColumn('is_playing');
           $table->dropColumn('enable_voting');
           $table->dropColumn('is_delete');
        });
    }
}
