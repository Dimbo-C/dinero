<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(QiwiWalletTypesTableSeeder::class);
        $this->call(QiwiWalletsTableSeeder::class);
        $this->call(AutowithdrawTypesTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        //        $this->call(QiwiWalletSettingsSeeder::class);
    }
}
