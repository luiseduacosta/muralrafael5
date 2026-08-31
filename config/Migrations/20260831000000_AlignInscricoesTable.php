<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Repairs the inscricoes table on databases aligned with production.
 *
 * The production table lost its primary key and AUTO_INCREMENT on id, which
 * breaks creating inscriptions: the ORM refuses inserts on tables without a
 * primary key and MySQL strict mode fails with "Field 'id' doesn't have a
 * default value". The stored ids show they were auto-generated before the
 * constraint was lost.
 *
 * The exact production column types are also applied with raw SQL so they
 * match the live schema: registro is INT(9), muralestagio_id is SMALLINT(3)
 * and the timestamp column auto-updates with ON UPDATE CURRENT_TIMESTAMP.
 *
 * On a fresh database built from the migrations the primary key statements
 * are a no-op: the Initial snapshot already creates inscricoes with an
 * auto-increment primary key.
 */
class AlignInscricoesTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('inscricoes');

        if (!$table->hasPrimaryKey(['id'])) {
            $this->execute(
                'ALTER TABLE `inscricoes` ADD PRIMARY KEY (`id`), '
                . 'MODIFY `id` INT NOT NULL AUTO_INCREMENT',
            );
        }

        // Exact column types present in production, applied with raw SQL so
        // they match the live schema.
        if ($table->hasColumn('registro')) {
            $this->execute(
                "ALTER TABLE `inscricoes` MODIFY `registro` INT(9) NOT NULL DEFAULT 0 COMMENT 'aluno_registro DRE'",
            );
        }

        if ($table->hasColumn('muralestagio_id')) {
            $this->execute(
                "ALTER TABLE `inscricoes` MODIFY `muralestagio_id` SMALLINT(3) NOT NULL DEFAULT 0 COMMENT 'ex id_instituicao'",
            );
        }

        if ($table->hasColumn('data')) {
            $this->execute("ALTER TABLE `inscricoes` MODIFY `data` DATE NOT NULL DEFAULT '0000-00-00'");
        }

        if ($table->hasColumn('periodo')) {
            $this->execute("ALTER TABLE `inscricoes` MODIFY `periodo` CHAR(6) NOT NULL DEFAULT ''");
        }

        if ($table->hasColumn('timestamp')) {
            $this->execute(
                'ALTER TABLE `inscricoes` MODIFY `timestamp` TIMESTAMP NOT NULL '
                . 'DEFAULT current_timestamp() ON UPDATE current_timestamp()',
            );
        }

        if ($table->hasColumn('aluno_id')) {
            $this->execute("ALTER TABLE `inscricoes` MODIFY `aluno_id` INT NOT NULL COMMENT 'ex alunonovo_id'");
        }

        if ($table->hasColumn('alunoestagiario_id')) {
            $this->execute('ALTER TABLE `inscricoes` MODIFY `alunoestagiario_id` INT NULL');
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('inscricoes');

        if ($table->hasPrimaryKey(['id'])) {
            $this->execute('ALTER TABLE `inscricoes` MODIFY `id` INT NOT NULL, DROP PRIMARY KEY');
        }
    }
}
