<?php
use Carbon\Carbon;

class QiwiWalletTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        $wallets = [
                ["Приемные киви", "receive"],
                ["Выводные киви", "output"],
                ["Резервные киви", "reserve"],
                ["Отработанные киви", "spent"],
                ["Автовыводные\\номер", "auto_withdraw_number"],
                ["Автовыводные\\карта", "auto_withdraw_card"],
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