<?php

/**
 * Created by IntelliJ IDEA.
 * User: root
 * Date: 01.09.17
 * Time: 17:15
 */
class QiwiWalletTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('roles')->insert(
                [
                        "name" => "Приемные киви",
                        "slug" => "receive",
                ],
                [
                        "name" => "Резервные киви",
                        "slug" => "reserve",
                ],
                [
                        "name" => "Выводные киви",
                        "slug" => "output",
                ],
                [
                        "name" => "Отработанные киви",
                        "slug" => "spent",
                ]
        );
    }
}