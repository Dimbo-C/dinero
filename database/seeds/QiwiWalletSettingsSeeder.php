<?php

class QiwiWalletSettingsSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 1,
                "balance_recheck_timeout" => 1
        ]);

        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 2,
                "balance_recheck_timeout" => 2
        ]);
    }
}