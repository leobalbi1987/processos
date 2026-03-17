<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\EmpenhoAcumulado;
use Illuminate\Database\Seeder;

class SyncEmpenhosAcumuladosSeeder extends Seeder
{
    public function run()
    {
        Empresa::all()->each(function ($empresa) {
            EmpenhoAcumulado::updateAcumulado($empresa->id);
        });
    }
}
