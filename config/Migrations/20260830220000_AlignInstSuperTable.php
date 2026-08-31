<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Repairs the inst_super join table (N:N between supervisores and
 * instituicoes) on databases aligned with production.
 *
 * The production table lost its primary key and AUTO_INCREMENT on id, which
 * breaks saving the belongsToMany association: inserts into inst_super fail
 * with "Field 'id' doesn't have a default value" under strict SQL mode. The
 * stored ids (2058 rows, all distinct) show they were auto-generated before
 * the constraint was lost.
 *
 * On a fresh database built from the migrations this is a no-op: the Initial
 * snapshot already creates inst_super with an auto-increment primary key.
 */
class AlignInstSuperTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('inst_super');

        if (!$table->hasPrimaryKey(['id'])) {
            $this->execute(
                'ALTER TABLE `inst_super` ADD PRIMARY KEY (`id`), '
                . 'MODIFY `id` INT NOT NULL AUTO_INCREMENT',
            );
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('inst_super');

        if ($table->hasPrimaryKey(['id'])) {
            $this->execute('ALTER TABLE `inst_super` MODIFY `id` INT NOT NULL, DROP PRIMARY KEY');
        }
    }
}
