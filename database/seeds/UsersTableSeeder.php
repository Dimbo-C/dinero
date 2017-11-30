<?php
use Carbon\Carbon;

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class UsersTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('users')->insert([
                "name" => "SuperAdmin",
                "email" => "admin@dinero.com",
                'password' => '$2y$10$psunb9pSP3OXBI3zyYyW1.P5YTCd0pF56MJdCPDpP73Wg5CJD25Jm',
                'remember_token' => 'KnnivXffUTFFstDz0m3YtyCo2sHqf1T2JDCrggpYoj6zakS9gZwQxKCJCB1v',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
                "name" => "operator",
                "email" => "operator1@dinero.com",
                'password' => '$2y$10$psunb9pSP3OXBI3zyYyW1.P5YTCd0pF56MJdCPDpP73Wg5CJD25Jm',
                'remember_token' => 'KnnivXffUTFFstDz0m3YtyCo2sHqf1T2JDCrggpYoj6zakS9gZwQxKCJCB1v',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);



//        DB::table('users')->insert([
//                "name" => "coo2571",
//                "head_id" => 1,
//                "email" => "",
//                'password' => "",
//        ]);
    }
}