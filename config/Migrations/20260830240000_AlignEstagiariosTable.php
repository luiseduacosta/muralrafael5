<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Repairs the estagiarios table on databases aligned with production.
 *
 * The production table lost its primary key and AUTO_INCREMENT on id, which
 * breaks creating internship records: inserts fail with "Field 'id' doesn't
 * have a default value" under strict SQL mode. The stored ids (8334 rows, all
 * distinct) show they were auto-generated before the constraint was lost.
 *
 * On a fresh database built from the migrations this is a no-op: the Initial
 * snapshot already creates estagiarios with an auto-increment primary key.
 */
class AlignEstagiariosTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('estagiarios');

        if (!$table->hasPrimaryKey(['id'])) {
            $this->execute(
                'ALTER TABLE `estagiarios` ADD PRIMARY KEY (`id`), '
                . 'MODIFY `id` INT NOT NULL AUTO_INCREMENT',
            );
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('estagiarios');

        if ($table->hasPrimaryKey(['id'])) {
            $this->execute('ALTER TABLE `estagiarios` MODIFY `id` INT NOT NULL, DROP PRIMARY KEY');
        }
    }
}
