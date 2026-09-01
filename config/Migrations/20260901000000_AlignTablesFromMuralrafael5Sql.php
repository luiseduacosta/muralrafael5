<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlignTablesFromMuralrafael5Sql extends BaseMigration
{
    /**
     * Up Method.
     *
     * Applies the schema changes described in muralrafael5.sql:
     *  - drop codigo_telefone/codigo_celular from alunos, professores, supervisores
     *  - rename respostas.questionario_id to respostas.questao_id
     *  - inst_super: drop default=0 on FK columns to match fixture/migration
     *
     * @return void
     */
    public function up(): void
    {
        $alunos = $this->table('alunos');
        if ($alunos->hasColumn('codigo_telefone')) {
            $alunos->removeColumn('codigo_telefone');
        }
        if ($alunos->hasColumn('codigo_celular')) {
            $alunos->removeColumn('codigo_celular');
        }
        if ($alunos->exists()) {
            $alunos->update();
        }

        $professores = $this->table('professores');
        if ($professores->hasColumn('codigo_telefone')) {
            $professores->removeColumn('codigo_telefone');
        }
        if ($professores->hasColumn('codigo_celular')) {
            $professores->removeColumn('codigo_celular');
        }
        if ($professores->exists()) {
            $professores->update();
        }

        $supervisores = $this->table('supervisores');
        if ($supervisores->hasColumn('codigo_telefone')) {
            $supervisores->removeColumn('codigo_telefone');
        }
        if ($supervisores->hasColumn('codigo_celular')) {
            $supervisores->removeColumn('codigo_celular');
        }
        if ($supervisores->exists()) {
            $supervisores->update();
        }

        $respostas = $this->table('respostas');
        if ($respostas->hasColumn('questionario_id') && !$respostas->hasColumn('questao_id')) {
            $this->execute(
                'ALTER TABLE `respostas` '
                . 'CHANGE COLUMN `questionario_id` `questao_id` INT(11) NOT NULL',
            );
        }

        $instSuper = $this->table('inst_super');
        if ($instSuper->hasColumn('instituicao_id')) {
            $this->execute(
                'ALTER TABLE `inst_super` MODIFY `instituicao_id` INT(4) NOT NULL',
            );
        }
        if ($instSuper->hasColumn('supervisor_id')) {
            $this->execute(
                'ALTER TABLE `inst_super` MODIFY `supervisor_id` INT(4) NOT NULL',
            );
        }
    }

    /**
     * Down Method.
     *
     * Reverses the changes (re-adds codigo_ columns, renames FK back).
     *
     * @return void
     */
    public function down(): void
    {
        $alunos = $this->table('alunos');
        if (!$alunos->hasColumn('codigo_telefone')) {
            $alunos->addColumn('codigo_telefone', 'tinyinteger', [
                'limit' => 2,
                'null' => true,
                'default' => 21,
                'signed' => false,
            ]);
        }
        if (!$alunos->hasColumn('codigo_celular')) {
            $alunos->addColumn('codigo_celular', 'tinyinteger', [
                'limit' => 2,
                'null' => true,
                'default' => 21,
                'signed' => false,
            ]);
        }
        if ($alunos->exists()) {
            $alunos->update();
        }

        $professores = $this->table('professores');
        if (!$professores->hasColumn('codigo_telefone')) {
            $professores->addColumn('codigo_telefone', 'char', [
                'limit' => 2,
                'null' => true,
                'default' => '21',
            ]);
        }
        if (!$professores->hasColumn('codigo_celular')) {
            $professores->addColumn('codigo_celular', 'char', [
                'limit' => 2,
                'null' => true,
                'default' => '21',
            ]);
        }
        if ($professores->exists()) {
            $professores->update();
        }

        $supervisores = $this->table('supervisores');
        if (!$supervisores->hasColumn('codigo_telefone')) {
            $supervisores->addColumn('codigo_telefone', 'smallinteger', [
                'null' => false,
                'default' => 0,
            ]);
        }
        if (!$supervisores->hasColumn('codigo_celular')) {
            $supervisores->addColumn('codigo_celular', 'smallinteger', [
                'null' => false,
                'default' => 0,
            ]);
        }
        if ($supervisores->exists()) {
            $supervisores->update();
        }

        $respostas = $this->table('respostas');
        if ($respostas->hasColumn('questao_id') && !$respostas->hasColumn('questionario_id')) {
            $this->execute(
                'ALTER TABLE `respostas` '
                . 'CHANGE COLUMN `questao_id` `questionario_id` INT(11) NOT NULL',
            );
        }
    }
}
