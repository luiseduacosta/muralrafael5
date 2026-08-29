<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Add estagiarios_count column to supervisores, professores, instituicoes and alunos
 * tables for the CounterCache behavior configured in EstagiariosTable.
 */
class AddEstagiariosCount extends BaseMigration
{
    /**
     * Up Method.
     *
     * Adds the estagiarios_count column to the four related tables and
     * populates it with the current count of estagiarios per record.
     *
     * @return void
     */
    public function up(): void
    {
        $foreignKeyMap = [
            'supervisores' => 'supervisor_id',
            'professores' => 'professor_id',
            'instituicoes' => 'instituicao_id',
            'alunos' => 'aluno_id',
        ];

        foreach ($foreignKeyMap as $tableName => $foreignKey) {
            $table = $this->table($tableName);

            if (!$table->hasColumn('estagiarios_count')) {
                $table->addColumn('estagiarios_count', 'integer', [
                    'null' => true,
                    'default' => 0,
                    'signed' => false,
                ]);
            }

            $table->update();

            // Populate the count with the actual number of estagiarios
            $this->execute(
                "UPDATE {$tableName} SET estagiarios_count = (
                    SELECT COUNT(*) FROM estagiarios
                    WHERE estagiarios.{$foreignKey} = {$tableName}.id
                )"
            );
        }
    }

    /**
     * Down Method.
     *
     * Removes the estagiarios_count column from the four related tables.
     *
     * @return void
     */
    public function down(): void
    {
        foreach (['supervisores', 'professores', 'instituicoes', 'alunos'] as $tableName) {
            $table = $this->table($tableName);

            if ($table->hasColumn('estagiarios_count')) {
                $table->removeColumn('estagiarios_count');
            }

            $table->update();
        }
    }
}
