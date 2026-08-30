<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Aligns the schema produced by the migration chain with the production
 * database:
 * - drops the usser_id typo column from professores (the association uses
 *   user_id);
 * - aligns the legacy docentes table with the professores schema present in
 *   production (the application no longer uses docentes; its fields were
 *   replaced by professores);
 * - re-applies the exact column types found in production (enum/set columns,
 *   mediumtext fields, timestamp defaults, siape widths) that the Initial
 *   snapshot stored as varchar(255)/text/NULL;
 * - makes the estagiarios_count columns signed with the exact nullability
 *   of each production table (alunos is NOT NULL, the others are nullable);
 * - adds the columns and foreign key of configuraplanejamentos that
 *   production has but the Initial snapshot omitted.
 *
 * All guards are no-ops on a fresh database built from the migrations.
 */
class FixEstagiariosCounterCacheColumns extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('professores');

        if ($table->hasColumn('usser_id')) {
            $table->removeColumn('usser_id');
            $table->update();
        }

        // observacoes: text -> mediumtext in production
        $this->execute('ALTER TABLE `professores` MODIFY `observacoes` MEDIUMTEXT NULL');

        $this->alignDocentes();
        $this->alignInstituicoes();
        $this->alignEstagiariosCount();
        $this->alignLegacyTimestampColumns();
        $this->alignLegacyTypes();
        $this->alignConfiguraplanejamentos();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('professores');

        if (!$table->hasColumn('usser_id')) {
            $table->addColumn('usser_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ]);
            $table->update();
        }
    }

    /**
     * Transforms the legacy 39-column docentes table into the 21-column
     * professores-like shape present in production.
     *
     * @return void
     */
    private function alignDocentes(): void
    {
        $table = $this->table('docentes');

        $toDrop = [
            'datanascimento',
            'localnascimento',
            'sexo',
            'homepage',
            'redesocial',
            'curriculosigma',
            'pesquisadordgp',
            'formacaoprofissional',
            'universidadedegraduacao',
            'anoformacao',
            'mestradoarea',
            'mestradouniversidade',
            'mestradoanoconclusao',
            'doutoradoarea',
            'doutoradouniversidade',
            'doutoradoanoconclusao',
            'formaingresso',
            'tipocargo',
            'categoria',
            'regimetrabalho',
        ];

        $changed = false;

        foreach ($toDrop as $column) {
            if ($table->hasColumn($column)) {
                $table->removeColumn($column);
                $changed = true;
            }
        }

        if ($table->hasColumn('ddd_telefone')) {
            $table->renameColumn('ddd_telefone', 'codigo_telefone');
            $changed = true;
        }

        if ($table->hasColumn('ddd_celular')) {
            $table->renameColumn('ddd_celular', 'codigo_celular');
            $changed = true;
        }

        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => false,
            ]);
            $changed = true;
        }

        if (!$table->hasColumn('status')) {
            $table->addColumn('status', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ]);
            $changed = true;
        }

        if (!$table->hasColumn('estagiarios_count')) {
            $table->addColumn('estagiarios_count', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => false,
            ]);
            $changed = true;
        }

        if ($changed) {
            $table->update();
        }

        // Exact column types present in production
        $this->execute('ALTER TABLE `docentes` MODIFY `cpf` CHAR(14) NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `telefone` VARCHAR(20) NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `celular` VARCHAR(20) NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `codigo_telefone` TINYINT UNSIGNED NOT NULL DEFAULT 21');
        $this->execute('ALTER TABLE `docentes` MODIFY `codigo_celular` TINYINT UNSIGNED NOT NULL DEFAULT 21');
        $this->execute('ALTER TABLE `docentes` MODIFY `status` VARCHAR(10) NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `estagiarios_count` INT UNSIGNED NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `siape` MEDIUMINT NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `observacoes` MEDIUMTEXT NULL');
    }

    /**
     * Adds the user_id column that exists in production but was not part of
     * the Initial snapshot. Not used by the application code.
     *
     * @return void
     */
    private function alignInstituicoes(): void
    {
        $table = $this->table('instituicoes');

        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ]);
            $table->update();
        }
    }

    /**
     * Matches the estagiarios_count columns to production: signed, default 0,
     * NOT NULL only on alunos (the other tables keep nullable counters).
     *
     * @return void
     */
    private function alignEstagiariosCount(): void
    {
        $this->execute('ALTER TABLE `alunos` MODIFY `estagiarios_count` INT NOT NULL DEFAULT 0');
        $this->execute('ALTER TABLE `professores` MODIFY `estagiarios_count` INT NULL DEFAULT 0');
        $this->execute('ALTER TABLE `supervisores` MODIFY `estagiarios_count` INT NULL DEFAULT 0');
        $this->execute('ALTER TABLE `instituicoes` MODIFY `estagiarios_count` INT NULL DEFAULT 0');
    }

    /**
     * Adds the columns that production has but the Initial snapshot omitted,
     * then applies their CURRENT_TIMESTAMP defaults.
     *
     * @return void
     */
    private function alignLegacyTimestampColumns(): void
    {
        $additions = [
            'dias' => [
                'created' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'modified' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
            ],
            'disciplinas' => [
                'created' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'modified' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'curriculo' => ['string', ['default' => null, 'limit' => 4, 'null' => true]],
            ],
            'planejamentos' => [
                'created' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'modified' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'observacoes' => ['string', ['default' => null, 'limit' => 255, 'null' => true]],
            ],
            'migration_backup_20260414' => [
                'backup_time' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
            ],
            'impersonations' => [
                'started_at' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
            ],
            'auth_users-bak' => [
                'criado_em' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
                'atualizado_em' => ['timestamp', ['default' => null, 'limit' => null, 'null' => true]],
            ],
        ];

        foreach ($additions as $tableName => $columns) {
            $table = $this->table($tableName);
            $changed = false;

            foreach ($columns as $column => $definition) {
                if (!$table->hasColumn($column)) {
                    $table->addColumn($column, $definition[0], $definition[1]);
                    $changed = true;
                }
            }

            if ($changed) {
                $table->update();
            }
        }

        // Defaults applied with raw SQL so they match production exactly
        $this->execute('ALTER TABLE `dias` MODIFY `created` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `dias` MODIFY `modified` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `disciplinas` MODIFY `created` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `disciplinas` MODIFY `modified` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `planejamentos` MODIFY `created` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `planejamentos` MODIFY `modified` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute(
            'ALTER TABLE `migration_backup_20260414` MODIFY `backup_time` TIMESTAMP NULL DEFAULT current_timestamp()',
        );
        $this->execute('ALTER TABLE `impersonations` MODIFY `started_at` TIMESTAMP NULL DEFAULT current_timestamp()');
        $this->execute('ALTER TABLE `auth_users-bak` MODIFY `criado_em` TIMESTAMP NULL DEFAULT current_timestamp()');
        $this->execute(
            'ALTER TABLE `auth_users-bak` MODIFY `atualizado_em` TIMESTAMP NULL DEFAULT current_timestamp()',
        );
    }

    /**
     * Re-applies the legacy column types found in production: enum/set
     * columns, mediumtext fields, CURRENT_TIMESTAMP defaults and small
     * varchar/int widths that the Initial snapshot stored differently.
     *
     * @return void
     */
    private function alignLegacyTypes(): void
    {
        $this->execute("ALTER TABLE `afastamentos` MODIFY `carater` ENUM('total','parcial') NOT NULL");

        foreach (
            [
            'alunos_etica',
            'alunos_etica_23042912',
            'alunos_ingresso',
            'alunos_ingresso-bak',
            'alunos_ingresso_10-09-2014',
            'alunos_ingresso_18042012',
            ] as $tableName
        ) {
            $this->execute("ALTER TABLE `{$tableName}` MODIFY `turno` ENUM('D','N') NOT NULL");
        }

        $this->execute("ALTER TABLE `auth_users` MODIFY `categoria` ENUM('1','2','3','4') NOT NULL DEFAULT '2'");
        $this->execute(
            "ALTER TABLE `auth_users` MODIFY `role` ENUM('admin','aluno','professor',"
            . "'supervisor') NOT NULL DEFAULT 'aluno'",
        );
        $this->execute(
            "ALTER TABLE `auth_users-bak` MODIFY `role` ENUM('admin','supervisor',"
            . "'docente','aluno') NULL DEFAULT 'aluno'",
        );
        $this->execute(
            "ALTER TABLE `essusuarios` MODIFY `setor` ENUM('funcionario','docente','aluno graduacao',"
            . "'aluno posgraduacao','institucional','sistema','outros') NOT NULL DEFAULT 'funcionario'",
        );
        $this->execute("ALTER TABLE `eusers` MODIFY `categoria` ENUM('1','2','3') NOT NULL DEFAULT '2'");
        $this->execute("ALTER TABLE `mural_estagios` MODIFY `local_inscricao` SET('0','1') NOT NULL DEFAULT '0'");
        $this->execute("ALTER TABLE `prof_disciplinas` MODIFY `turno` ENUM('diurno','noturno') NOT NULL");
        $this->execute(
            "ALTER TABLE `usuarios` MODIFY `role` ENUM('READ ONLY','NO ACCESS','ADMIN') NOT NULL DEFAULT 'READ ONLY'",
        );
        $this->execute(
            "ALTER TABLE `viradas` MODIFY `periodo` ENUM('1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL",
        );
        $this->execute(
            "ALTER TABLE `viradas` MODIFY `situacao` ENUM('1','2','3','4','5','6','7') NOT NULL DEFAULT '1'",
        );

        // mediumtext columns
        $this->execute('ALTER TABLE `curso_inscricao_supervisor` MODIFY `observacoes` MEDIUMTEXT NULL');
        $this->execute('ALTER TABLE `monografia` MODIFY `resumo` MEDIUMTEXT NOT NULL');
        $this->execute('ALTER TABLE `monografia_20120910` MODIFY `resumo` MEDIUMTEXT NOT NULL');
        $this->execute('ALTER TABLE `monografias` MODIFY `resumo` MEDIUMTEXT NOT NULL');
        $this->execute('ALTER TABLE `mural_estagios` MODIFY `outras` MEDIUMTEXT NULL');
        $this->execute('ALTER TABLE `supervisores` MODIFY `observacoes` MEDIUMTEXT NULL');
        $this->execute('ALTER TABLE `turnos` MODIFY `turno` MEDIUMTEXT NOT NULL');

        // CURRENT_TIMESTAMP defaults for columns created by the Initial snapshot
        $this->execute('ALTER TABLE `turmaotps` MODIFY `created` DATETIME NULL DEFAULT current_timestamp()');
        $this->execute(
            'ALTER TABLE `turmaotps` MODIFY `modified` DATETIME NULL DEFAULT current_timestamp() '
            . 'ON UPDATE current_timestamp()',
        );
        $this->execute(
            'ALTER TABLE `balcao_users` MODIFY `timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp() '
            . 'ON UPDATE current_timestamp()',
        );

        // Widths and nullability
        $this->execute('ALTER TABLE `disciplinas` MODIFY `disciplina` VARCHAR(60) NULL');
        $this->execute('ALTER TABLE `salas` MODIFY `sala` VARCHAR(20) NOT NULL');
        $this->execute('ALTER TABLE `salas` MODIFY `lotacao` INT NULL');
        $this->execute('ALTER TABLE `salas` MODIFY `observacoes` VARCHAR(255) NULL');
        $this->execute('ALTER TABLE `salas` MODIFY `recursos` VARCHAR(255) NULL');
        $this->execute('ALTER TABLE `docentes` MODIFY `siape` MEDIUMINT NULL');
        $this->execute('ALTER TABLE `professores` MODIFY `siape` MEDIUMINT NULL');

        // Zero-date defaults kept by the legacy monografias table
        $this->execute("ALTER TABLE `monografias` MODIFY `data` DATE NOT NULL DEFAULT '0000-00-00'");
        $this->execute(
            "ALTER TABLE `monografias` MODIFY `timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' "
            . 'ON UPDATE current_timestamp()',
        );
    }

    /**
     * Adds the columns and the user foreign key that production's
     * configuraplanejamentos has but the Initial snapshot omitted.
     *
     * @return void
     */
    private function alignConfiguraplanejamentos(): void
    {
        $table = $this->table('configuraplanejamentos');
        $changed = false;

        $columns = [
            'usuarioplanejamento_id' => [
                'type' => 'integer',
                'options' => ['default' => null, 'limit' => null, 'null' => true, 'signed' => true],
            ],
            'nome' => ['type' => 'string', 'options' => ['default' => null, 'limit' => 20, 'null' => true]],
            'ativo' => [
                'type' => 'tinyinteger',
                'options' => ['default' => 0, 'limit' => null, 'null' => true, 'signed' => true],
            ],
            'created' => ['type' => 'datetime', 'options' => ['default' => null, 'limit' => null, 'null' => true]],
            'modified' => ['type' => 'datetime', 'options' => ['default' => null, 'limit' => null, 'null' => true]],
        ];

        foreach ($columns as $column => $definition) {
            if (!$table->hasColumn($column)) {
                $table->addColumn($column, $definition['type'], $definition['options']);
                $changed = true;
            }
        }

        if ($changed) {
            $table->update();
        }

        // Defaults applied with raw SQL so they match production exactly
        $this->execute(
            'ALTER TABLE `configuraplanejamentos` MODIFY `created` DATETIME NULL DEFAULT current_timestamp()',
        );
        $this->execute(
            'ALTER TABLE `configuraplanejamentos` MODIFY `modified` DATETIME NULL DEFAULT current_timestamp() '
            . 'ON UPDATE current_timestamp()',
        );

        if (!$table->hasForeignKey('usuarioplanejamento_id')) {
            $table->addForeignKey(
                'usuarioplanejamento_id',
                'users',
                'id',
                [
                    'constraint' => 'fk_configura_usuario',
                    'delete' => 'RESTRICT',
                    'update' => 'RESTRICT',
                ],
            );
            $table->update();
        }
    }
}
