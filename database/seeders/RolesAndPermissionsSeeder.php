<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Resetar permissões em cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Coleção de modelos para os quais vamos criar permissões
        $collection = collect([
            'Categorias',
            'Empenhos',
            'Empresas',
            'NotaFiscais',
            'Processos',
            'ProcessoMaes',
            'ProcessoStatusHistoricos',
            'Secretarias',
            'Status',
            'Tipos',
            'Users',
        ]);

        // Criar permissões para cada item da coleção
        $collection->each(function ($item) {
            // Criar permissões para visualização, criação, atualização, exclusão
            $permissions = [
                'viewAny' . $item,  // Visualizar lista
                'view' . $item,     // Visualizar item específico
                'create' . $item,   // Criar novo item
                'update' . $item,   // Editar item
                'delete' . $item,   // Deletar item
            ];

            // Verificar se a permissão já existe antes de criá-la
            foreach ($permissions as $permissionName) {
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['group' => $item, 'name' => $permissionName]);
                }
            }
        });

        // Criar a função super-admin e atribuir todas as permissões
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Criar a função admin e atribuir todas as permissões
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Atribuir a função super-admin ao usuário com e-mail
        $user = \App\Models\User::where('email', 'leogobalbi@gmail.com')->first();
        if ($user) {
            $user->assignRole('super-admin');
        } else {
            // Log para caso o usuário não seja encontrado
            Log::info("Usuário com e-mail leogobalbi@gmail.com não encontrado.");
        }
    }
}
