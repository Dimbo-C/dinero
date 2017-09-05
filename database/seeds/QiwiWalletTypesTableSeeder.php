<?php

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class QiwiWalletTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Приемные киви",
                "slug" => "receive",

        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Резервные киви",
                "slug" => "reserve",
        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Выводные киви",
                "slug" => "output",
        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Отработанные киви",
                "slug" => "spent",
        ]);
    }
}