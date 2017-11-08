<?php

class AutowithdrawTypesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        DB::table('autowithdraw_types')->insert([
                "type" => "Каждые Х минут",
                "slug" => "every_x_minutes",
        ]);
        DB::table('autowithdraw_types')->insert([
                "type" => "После каждого обновления баланса",
                "slug" => "after_balance_update",
        ]);
        DB::table('autowithdraw_types')->insert([
                "type" => "Вручную",
                "slug" => "manually",
        ]);
    }
}