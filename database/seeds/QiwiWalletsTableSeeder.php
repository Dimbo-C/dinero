<?php
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletSecuritySettings;
use Carbon\Carbon;

class QiwiWalletsTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {

        $wallets = [
                ["Main", "+380960968460", "Crixalis2204", 0],
                ["Anna", "+380507308340", "1956Ujl", 0],
                ["Eugene", "+380639720273", "!%)@!((%Jack/15", 0],
                ["Russ 1", "+79068369184", "EtvtiL4EDO", 1],
                ["Russ 2", "+79096127856", "F3Eu7F2iaK", 1],
                ["Russ 3", "+79619377629", "gIDMS1Aa08", 1],
                ["Russ 4", "+79619386097", "PY1x7aPPnL", 1],
                ["Russ 5", "+79789520267", "Qwerty1", 1],
        ];
        $proxies = [
                ["", "", "", ""],
                ["", "", "", ""],
                ["", "", "", ""],
                ["5.8.66.122", "8239", "user6760", "0ajq7n"],
                ["5.8.61.107", "8239", "user6760", "0ajq7n"],
                ["5.8.61.109", "8239", "user6760", "0ajq7n"],
                ["5.8.61.145", "8239", "user6760", "0ajq7n"],
                ["5.8.61.145", "8239", "user6760", "0ajq7n"],
        ];

        for ($i = 0; $i < count($wallets); $i++) {
            // proxies
            Proxy::insert([
                    "host" => $proxies[$i][0],
                    "port" => $proxies[$i][1],
                    "login" => $proxies[$i][2],
                    "password" => $proxies[$i][3],
            ]);

            //wallets
            Qiwiwallet::insert([
                    "type_id" => 1,
                    "name" => $wallets[$i][0],
                    "login" => $wallets[$i][1],
                    "password" => $wallets[$i][2],
                    "use_proxy" => $wallets[$i][3],
                    "is_active" => 1,
                    "proxy_id" => $i + 1,

                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            // settings
            DB::table('qiwi_wallet_settings')->insert([
                    "wallet_id" => $i + 1,
                    "balance_recheck_timeout" => 0,
                    "autoWithdrawal_type_id" => 2
            ]);

            // security
            QiwiWalletSecuritySettings::insert([
                    'wallet_id' => $i + 1,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}