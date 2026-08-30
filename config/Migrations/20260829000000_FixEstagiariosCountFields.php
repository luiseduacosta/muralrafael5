<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Drops the singular estagiario_count column that existed in the legacy
 * database. The CounterCache behavior uses the plural estagiarios_count
 * column, which is added by AddEstagiariosCount (20260803211411). All
 * guards are no-ops on a fresh database built from the migrations.
 */
class FixEstagiariosCountFields extends BaseMigration
{
    private const TABLES = ['professores', 'supervisores', 'alunos'];

    /**
     * @return void
     */
    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            $table = $this->table($tableName);

            if ($table->hasColumn('estagiario_count')) {
                $table->removeColumn('estagiario_count');
                $table->update();
            }
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            $table = $this->table($tableName);

            if (!$table->hasColumn('estagiario_count')) {
                $table->addColumn('estagiario_count', 'integer', [
                    'default' => 0,
                    'limit' => null,
                    'null' => true,
                    'signed' => false,
                ]);
                $table->update();
            }
        }
    }
}
