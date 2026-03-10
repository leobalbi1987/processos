<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cria o papel super-admin se não existir
        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Cria ou atualiza o usuário
        $user = User::updateOrCreate(
            ['email' => 'leogobalbi@gmail.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('123456789')]
        );

        // Atribui o papel
        $user->assignRole($role);
    }
}
