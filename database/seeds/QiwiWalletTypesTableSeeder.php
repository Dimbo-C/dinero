<?php

use Carbon\Carbon;

class QiwiWalletTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//
//
//        DB::table("qiwi_wallet_types")->truncate();
//
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//
//        // reset index
//        $statement = "ALTER TABLE qiwi_wallet_types AUTO_INCREMENT = 1;";
//        DB::unprepared($statement);
        $wallets = [
                ["Приемные киви", "receive"],
                ["Выводные киви", "output"],
                ["Автовыводные\\номер", "auto_withdraw_number"],
                ["Автовыводные\\карта", "auto_withdraw_card"],
                ["Резервные киви", "reserve"],
                ["Отработанные киви", "spent"],
        ];

        foreach ($wallets as $wallet) {
            DB::table('qiwi_wallet_types')->insert([
                    "name" => $wallet[0],
                    "slug" => $wallet[1],

                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

    }
}