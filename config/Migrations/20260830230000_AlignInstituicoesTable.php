<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Repairs the instituicoes table on databases aligned with production.
 *
 * The production table lost its primary key and AUTO_INCREMENT on id, which
 * breaks creating institutions: inserts fail with "Field 'id' doesn't have a
 * default value" under strict SQL mode. The stored ids (656 rows, all
 * distinct) show they were auto-generated before the constraint was lost.
 *
 * The column order of user_id/estagiarios_count is also aligned with
 * production (user_id before estagiarios_count).
 *
 * On a fresh database built from the migrations the primary key statements are
 * a no-op: the Initial snapshot already creates instituicoes with an
 * auto-increment primary key.
 */
class AlignInstituicoesTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('instituicoes');

        if (!$table->hasPrimaryKey(['id'])) {
            $this->execute(
                'ALTER TABLE `instituicoes` ADD PRIMARY KEY (`id`), '
                . 'MODIFY `id` INT NOT NULL AUTO_INCREMENT',
            );
        }

        if ($table->hasColumn('user_id') && $table->hasColumn('estagiarios_count')) {
            $this->execute(
                'ALTER TABLE `instituicoes` MODIFY `user_id` INT NOT NULL DEFAULT 0 AFTER `observacoes`, '
                . 'MODIFY `estagiarios_count` INT NULL DEFAULT 0 AFTER `user_id`',
            );
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('instituicoes');

        if ($table->hasPrimaryKey(['id'])) {
            $this->execute('ALTER TABLE `instituicoes` MODIFY `id` INT NOT NULL, DROP PRIMARY KEY');
        }
    }
}
