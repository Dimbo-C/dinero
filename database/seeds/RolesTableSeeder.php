<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends \Illuminate\Database\Seeder {
    public function run() {
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        Permission::create(['name' => 'edit wallets']);
        Permission::create(['name' => 'add wallets']);
        Permission::create(['name' => 'delete wallets']);

        $role = Role::create([
                'name' => 'Главный администратор',
                "guard_name" => "web"
        ]);

        $regManager = Role::create([
                'name' => 'Региональный менеджер',
                "guard_name" => "regional_manager"
        ]);

        $deputyDirector = Role::create([
                'name' => 'Зам. директора',
                "guard_name" => "deputy_director"
        ]);

        $kurator = Role::create([
                'name' => 'Куратор',
                "guard_name" => "curator"
        ]);

        $salesManager = Role::create([
                'name' => 'Менеджер по продажам',
                "guard_name" => "web"
        ]);

        $role->givePermissionTo('edit wallets');
        $role->givePermissionTo('add wallets');
        $role->givePermissionTo('delete wallets');

//        $salesManager->

        // create
        $user = \App\User::whereName("SuperAdmin")->first();
        $user->assignRole("Главный администратор");

        $manager = \App\User::whereName("operator")->first();
        $manager->assignRole("Менеджер по продажам");
    }
}