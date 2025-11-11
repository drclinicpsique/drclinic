<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Tente remover o índice UNIQUE do campo 'crm' (sem Doctrine)
        // Possíveis nomes de índice em MySQL:
        // - usuarios_crm_unique (padrão do Laravel)
        // - crm_unique
        // - usuarios_crm_uindex (variações)
        // Usamos tentativas em sequência com try/catch silencioso.

        $possibleIndexNames = [
            'usuarios_crm_unique',
            'crm_unique',
            'usuarios_crm_uindex',
            'crm', // em alguns ambientes, o índice pode ter sido nomeado como o próprio campo
        ];

        foreach ($possibleIndexNames as $index) {
            try {
                DB::statement("ALTER TABLE `usuarios` DROP INDEX `$index`");
            } catch (\Throwable $e) {
                // ignora: se não existir, continua tentando os próximos
            }
        }

        // 2) Remover a coluna 'crm' se existir
        if (Schema::hasColumn('usuarios', 'crm')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('crm');
            });
        }
    }

    public function down(): void
    {
        // Recria a coluna 'crm' com UNIQUE (opcional para rollback)
        if (!Schema::hasColumn('usuarios', 'crm')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('crm', 20)->nullable()->unique();
            });
        }
    }
};