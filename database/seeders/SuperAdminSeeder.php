<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::create([
            'name' => 'SuperAdmin',
            'email'=> 'Superadmin@gmail.com',
            'password' => bcrypt('1234')
        ]);

        $rol = Role::create(['name' => 'administrador']);
        $permisos = Permission::pluck('id', 'id')->all();
        $rol->syncPermissions($permisos);
        $usuario->assignRole([$rol->id]);

    }
}
