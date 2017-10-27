<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQiwiSettingsAutowithdrawWallets extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('qiwi_settings_autowithdraw_wallets', function (Blueprint $table) {
            $table->integer("master_wallet_id")->unsigned();
            $table->integer('autowithdraw_wallet_id')->unsigned();
            $table->foreign('master_wallet_id','mv_id_w_id')->references('wallet_id')->on('qiwi_wallet_settings')->onDelete('cascade');
            $table->foreign('autowithdraw_wallet_id', 'a_w_id_q_a_w')->references('id')->on('qiwi_autowithdraw_wallets')->onDelete('cascade');
        });

//        Schema::table('qiwi_autowithdraw_wallets', function ($table) {
//            $table->foreign('id')->references("id")->on("qiwi_wallets")->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('qiwi_settings_autowithdraw_wallets');
    }
}
