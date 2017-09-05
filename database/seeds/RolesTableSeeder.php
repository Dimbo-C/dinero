<?php

class RolesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('roles')->insert([
                "name" => "superadmin",
                "guard_name" => "web"
        ]);
        DB::table('roles')->insert([
                "name" => "Региональный менеджер",
                "guard_name" => "web"
        ]);
        DB::table('roles')->insert([
                "name" => "Зам. директора",
                "guard_name" => "web"
        ]);
        DB::table('roles')->insert([
                "name" => "Куратор",
                "guard_name" => "web"
        ]);
        DB::table('roles')->insert([
                "name" => "Менеджер по продажам",
                "guard_name" => "web"
        ]);
    }
}