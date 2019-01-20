<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alterplaying11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('playing11', function (Blueprint $table) {
           $table->integer('is_captain')->nullable();
           $table->integer('is_wk')->nullable();
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
        Schema::table('playing11', function (Blueprint $table) {
            $table->dropColumn('is_captain');
            $table->dropColumn('is_wk');
        });
    }
}
