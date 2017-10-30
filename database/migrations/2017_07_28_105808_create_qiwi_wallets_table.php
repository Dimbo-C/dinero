<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQiwiWalletsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('qiwi_wallets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('type_id')->unsigned()->index();
            $table->foreign('type_id')->references('id')->on('qiwi_wallet_types')->onDelete('cascade');
            $table->boolean('use_proxy')->default(false);

            $table->integer('proxy_id')->nullable()->unsigned()->index();
            $table->foreign('proxy_id')->references('id')->on('proxies')->onDelete('cascade');

            $table->string('name');
            $table->string('login');
            $table->string('password');
            $table->string('balance')->default(0);
            $table->string('month_income')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('qiwi_wallets');
    }
}
