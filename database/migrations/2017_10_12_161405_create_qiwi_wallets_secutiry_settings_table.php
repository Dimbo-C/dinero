<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQiwiWalletsSecutirySettingsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('qiwi_wallet_security_settings', function (Blueprint $table) {
            $table->integer('wallet_id')->unsigned()->index();
            $table->foreign('wallet_id')->references("id")->on("qiwi_wallets")->onDelete('cascade');
            $table->boolean('sms_confirmation')->default(false);
            $table->boolean('email_binding')->default(false);
            $table->string('email')->default("");
            $table->boolean('use_token')->default(false);
            $table->boolean('use_pin_code')->default(false);
            $table->boolean('sms_payments')->default(false);
            $table->boolean('call_confirm')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('qiwi_wallet_security_settings');
    }
}
