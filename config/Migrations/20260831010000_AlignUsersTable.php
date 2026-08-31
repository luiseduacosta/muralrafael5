<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Aligns the users table with the production (ess_apps) schema.
 *
 * Columns are coerced to their live MySQL definitions:
 *   - id            INT(11) NOT NULL AUTO_INCREMENT
 *   - email         CHAR(50) NULL
 *   - password      CHAR(80) NULL
 *   - nome          VARCHAR(128) NULL  (comment: Nome do usuário)
 *   - role          ENUM('admin','supervisor','professor','aluno') DEFAULT 'aluno'
 *   - categoria     ENUM('1','2','3','4') NOT NULL DEFAULT '2'
 *   - identificacao INT(9) NULL         (comment: Registro do aluno, SIAPE do professor ou CRESS do supervisor)
 *   - entidade_id   INT(11) NULL        (comment: id da entidade: aluno, professor ou supervisor)
 *   - ativo         TINYINT(1) DEFAULT 1
 *   - criado_em     TIMESTAMP NOT NULL DEFAULT current_timestamp()
 *   - atualizado_em TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
 *   - aluno_id      INT(11) NULL
 *   - supervisor_id INT(11) NULL
 *   - professor_id  INT(11) NULL
 *
 * Every ALTER is guarded by a hasColumn check so this is a no-op on a
 * database that already matches production.
 */
class AlignUsersTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('users');

        if ($table->hasColumn('email')) {
            $this->execute('ALTER TABLE `users` MODIFY `email` CHAR(50) NULL');
        }

        if ($table->hasColumn('password')) {
            $this->execute('ALTER TABLE `users` MODIFY `password` CHAR(80) NULL');
        }

        if ($table->hasColumn('nome')) {
            $this->execute(
                "ALTER TABLE `users` MODIFY `nome` VARCHAR(128) NULL COMMENT 'Nome do usuário'",
            );
        }

        if ($table->hasColumn('role')) {
            $this->execute(
                "ALTER TABLE `users` MODIFY `role` ENUM('admin','supervisor','professor','aluno') DEFAULT 'aluno'",
            );
        }

        if ($table->hasColumn('categoria')) {
            $this->execute(
                "ALTER TABLE `users` MODIFY `categoria` ENUM('1','2','3','4') NOT NULL DEFAULT '2'",
            );
        }

        if ($table->hasColumn('identificacao')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `identificacao` INT(9) NULL '
                . "COMMENT 'Registro do aluno, SIAPE do professor ou CRESS do supervisor'",
            );
        }

        if ($table->hasColumn('entidade_id')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `entidade_id` INT(11) NULL '
                . "COMMENT 'id da entidade: aluno, professor ou supervisor'",
            );
        }

        if ($table->hasColumn('ativo')) {
            $this->execute('ALTER TABLE `users` MODIFY `ativo` TINYINT(1) DEFAULT 1');
        }

        if ($table->hasColumn('criado_em')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `criado_em` TIMESTAMP NOT NULL DEFAULT current_timestamp()',
            );
        }

        if ($table->hasColumn('atualizado_em')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `atualizado_em` TIMESTAMP NOT NULL '
                . 'DEFAULT current_timestamp() ON UPDATE current_timestamp()',
            );
        }

        if ($table->hasColumn('aluno_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `aluno_id` INT(11) NULL');
        }

        if ($table->hasColumn('supervisor_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `supervisor_id` INT(11) NULL');
        }

        if ($table->hasColumn('professor_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `professor_id` INT(11) NULL');
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('users');

        if ($table->hasColumn('email')) {
            $this->execute('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
        }

        if ($table->hasColumn('password')) {
            $this->execute('ALTER TABLE `users` MODIFY `password` VARCHAR(255) NULL');
        }

        if ($table->hasColumn('nome')) {
            $this->execute('ALTER TABLE `users` MODIFY `nome` VARCHAR(255) NULL');
        }

        if ($table->hasColumn('role')) {
            $this->execute("ALTER TABLE `users` MODIFY `role` VARCHAR(255) NULL DEFAULT 'aluno'");
        }

        if ($table->hasColumn('categoria')) {
            $this->execute("ALTER TABLE `users` MODIFY `categoria` VARCHAR(255) NOT NULL DEFAULT '2'");
        }

        if ($table->hasColumn('identificacao')) {
            $this->execute('ALTER TABLE `users` MODIFY `identificacao` INT NULL');
        }

        if ($table->hasColumn('entidade_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `entidade_id` INT NULL');
        }

        if ($table->hasColumn('ativo')) {
            $this->execute('ALTER TABLE `users` MODIFY `ativo` TINYINT(1) NULL DEFAULT 1');
        }

        if ($table->hasColumn('criado_em')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `criado_em` TIMESTAMP NOT NULL DEFAULT current_timestamp()',
            );
        }

        if ($table->hasColumn('atualizado_em')) {
            $this->execute(
                'ALTER TABLE `users` MODIFY `atualizado_em` TIMESTAMP NOT NULL DEFAULT current_timestamp()',
            );
        }

        if ($table->hasColumn('aluno_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `aluno_id` INT NULL');
        }

        if ($table->hasColumn('supervisor_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `supervisor_id` INT NULL');
        }

        if ($table->hasColumn('professor_id')) {
            $this->execute('ALTER TABLE `users` MODIFY `professor_id` INT NULL');
        }
    }
}
