<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletSettingsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('qiwi_wallet_settings', function (Blueprint $table) {
            $table->integer('wallet_id')->unsigned()->index();
            $table->foreign('wallet_id')->references("id")->on("qiwi_wallets")->onDelete('cascade');
            $table->string('comments')->nullable();
            $table->boolean('is_always_online')->default(0);
            $table->integer('balance_recheck_timeout')->default(0);
            $table->timestamp('last_balance_recheck')->useCurrent();
            $table->double('maximum_balance')->default(floatval(1000000.0));
            $table->boolean('autoWithdrawal_active')->default(false);
            $table->string('autoWithdrawal_target')->default("card");

            $table->integer('autoWithdrawal_type_id')->nullable()->unsigned()->index();
            $table->foreign('autoWithdrawal_type_id')->references("id")->on("autowithdraw_types")->onDelete('cascade');
            $table->integer('autoWithdrawal_minutes')->default(0);
            $table->double('autoWithdrawal_minimum_withdraw_amount')->default(2500);
            $table->double('autoWithdrawal_limit')->default(14500);
            $table->timestamp('last_withdrawal_time')->useCurrent();

            $table->boolean('using_vouchers')->default(false);
            $table->string('autoWithdrawal_card_number')->nullable();
            $table->string('autoWithdrawal_cardholder_name')->nullable();
            $table->string('autoWithdrawal_cardholder_surname')->nullable();
            $table->string('autoWithdrawal_wallet_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists("qiwi_wallet_settings");
    }
}
