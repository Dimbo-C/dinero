<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQiwiAutowithdrawWallets extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('qiwi_autowithdraw_wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->string("number");

            $table->timestamps();
        });

        Schema::table('qiwi_autowithdraw_wallets', function ($table) {
            $table->foreign('id')->references("id")->on("qiwi_wallets")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('qiwi_autowithdraw_wallets');
    }
}
