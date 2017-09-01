<?php

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class UsersTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('roles')->insert(
                [
                        "name" => "SuperAdmin",
                        "email" => "admin@dinero.com",
                        'password' => '$2y$10$psunb9pSP3OXBI3zyYyW1.P5YTCd0pF56MJdCPDpP73Wg5CJD25Jm',
                        'remember_token' => 'KnnivXffUTFFstDz0m3YtyCo2sHqf1T2JDCrggpYoj6zakS9gZwQxKCJCB1v'
                ]
        );
    }
}