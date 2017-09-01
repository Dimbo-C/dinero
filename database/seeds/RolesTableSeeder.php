<?php

class RolesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('roles')->insert(
                [
                        "name" => "superadmin",
                        "guard_name" => "web"
                ],
                [
                        "name" => "Региональный менеджер",
                        "guard_name" => "web"
                ],
                [
                        "name" => "Зам. директора",
                        "guard_name" => "web"
                ],
                [
                        "name" => "Куратор",
                        "guard_name" => "web"
                ],
                [
                        "name" => "Менеджер по продажам",
                        "guard_name" => "web"
                ]
        );
    }
}