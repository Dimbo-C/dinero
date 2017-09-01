<?php

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
        Schema::create('wallet_settings', function (Blueprint $table) {
            $table->integer('wallet_id')->unsigned()->index();
            $table->string('comments')->nullable();
            $table->boolean('is_always_online')->nullable();
            $table->integer('recheck_balance_timeout')->default(0);
            $table->float('maximum_balance')->default(1000 * 1000);
            $table->boolean('is_autowithdraw')->default(false);
            $table->integer('autowithdraw_type_id')->references("id")->on("autowithdraw_types")->onDelete('cascade');
            $table->boolean('is_voucher')->default(false);
            $table->string('autowithdraw_card_number')->nullable();
            $table->string('autowithdraw_owner_name')->nullable();
            $table->string('autowithdraw_owner_surname')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists("wallet_settings");
    }
}
