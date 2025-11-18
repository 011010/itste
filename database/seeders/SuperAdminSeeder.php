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
        // Usar variables de entorno para credenciales del SuperAdmin
        // Si no están definidas, usar valores por defecto (solo para desarrollo)
        $usuario = User::create([
            'name' => env('SUPERADMIN_NAME', 'SuperAdmin'),
            'email'=> env('SUPERADMIN_EMAIL', 'admin@example.com'),
            'password' => bcrypt(env('SUPERADMIN_PASSWORD', 'ChangeThisPassword123!'))
        ]);

        $rol = Role::create(['name' => 'administrador']);
        $permisos = Permission::pluck('id', 'id')->all();
        $rol->syncPermissions($permisos);
        $usuario->assignRole([$rol->id]);
    }
}
