<?php
use Carbon\Carbon;

class QiwiWalletTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Приемные киви",
                "slug" => "receive",

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Резервные киви",
                "slug" => "reserve",

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Выводные киви",
                "slug" => "output",

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallet_types')->insert([
                "name" => "Отработанные киви",
                "slug" => "spent",

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}