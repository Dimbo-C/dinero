<?php
use App\QiwiWalletSecuritySettings;
use Carbon\Carbon;

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class QiwiWalletsTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        for ($i = 0; $i < 3; $i++) {
            DB::table('proxies')->insert([
                    "type" => ""
            ]);
        }

        DB::table('proxies')->insert([
                "host" => "5.8.66.122",
                "port" => "8239",
                "login" => "user6760",
                "password" => "0ajq7n",
        ]);


        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Main Wallet",
                "login" => "+380960968460",
                "password" => "Crixalis2204",
                "is_active" => 1,
                "proxy_id" => 1,

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Anna Wallet",
                "login" => "+380507308340",
                "password" => "1956Ujl",
                "is_active" => 1,
                "proxy_id" => 2,

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Eugene Wallet",
                "login" => "+380639720273",
                "password" => "!%)@!((%Jack/15",
                "is_active" => 1,
                "proxy_id" => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('qiwi_wallets')->insert([
                "type_id" => 1,
                "name" => "Wallet under proxy",
                "login" => "+79068369184",
                "password" => "EtvtiL4EDO",
                "use_proxy" => 1,
                "is_active" => 1,
                "proxy_id" => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 1,
                "balance_recheck_timeout" => 0,
                "autoWithdrawal_type_id" => 2,
        ]);
        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 2,
                "balance_recheck_timeout" => 0,
                "autoWithdrawal_type_id" => 2
        ]);
        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 3,
                "balance_recheck_timeout" => 0,
                "autoWithdrawal_type_id" => 2
        ]);
        DB::table('qiwi_wallet_settings')->insert([
                "wallet_id" => 4,
                "balance_recheck_timeout" => 0,
                "autoWithdrawal_type_id" => 2
        ]);

        for ($i = 1; $i <= 4; $i++) {
            QiwiWalletSecuritySettings::insert([
                    'wallet_id' => $i,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}