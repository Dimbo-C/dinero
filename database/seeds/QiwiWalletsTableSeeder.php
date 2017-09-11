<?php
use Carbon\Carbon;

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class QiwiWalletsTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Main Wallet",
                "login" => "+380960968460",
                "password" => "Kekroach2204",
                "is_active" => 1,

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Anna Wallet",
                "login" => "+380507308340",
                "password" => "1956Ujl",
                "is_active" => 1,

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}