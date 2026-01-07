<?php

namespace Database\Seeders;

use App\Models\ContactFunction;
use Illuminate\Database\Seeder;

class ContactFunctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $functions = [
            'Gerente',
            'Diretor',
            'Administrador',
            'Gestor',
            'Coordenador',
            'Responsável de Compras',
            'Responsável de Vendas',
            'Responsável Financeiro',
            'Responsável Técnico',
            'Administrativo',
            'Comercial',
            'Técnico',
            'Contabilista',
            'Recursos Humanos',
            'Marketing',
            'Logística',
            'IT / Informática',
            'Qualidade',
            'Produção',
            'Manutenção',
        ];

        foreach ($functions as $function) {
            ContactFunction::create([
                'name' => $function,
                'active' => true,
            ]);
        }
    }
}
