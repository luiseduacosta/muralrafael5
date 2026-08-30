<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Adds the status and estagiarios_count columns to professores, matching the
 * production schema (status defaults to 'ativo').
 */
class AddStatusAndEstagiariosCountToProfessores extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('professores');

        if (!$table->hasColumn('status')) {
            $table->addColumn('status', 'string', [
                'default' => 'ativo',
                'limit' => 10,
                'null' => false,
            ]);
        }

        if (!$table->hasColumn('estagiarios_count')) {
            $table->addColumn('estagiarios_count', 'integer', [
                'default' => 0,
                'limit' => null,
                'null' => true,
                'signed' => false,
            ]);
        }

        $table->update();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('professores');

        if ($table->hasColumn('status')) {
            $table->removeColumn('status');
        }

        if ($table->hasColumn('estagiarios_count')) {
            $table->removeColumn('estagiarios_count');
        }

        $table->update();
    }
}
