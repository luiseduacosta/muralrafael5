<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Aligns the supervisores schema with the production database:
 * - estagiarios_count is unsigned in production (int(10) unsigned) but the
 *   migration chain produced a signed column.
 *
 * All guards are no-ops on a database that already matches production.
 */
class AlignSupervisoresTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('supervisores');

        // Exact column type present in production, applied with raw SQL so
        // it matches the live schema.
        if ($table->hasColumn('estagiarios_count')) {
            $this->execute('ALTER TABLE `supervisores` MODIFY `estagiarios_count` INT UNSIGNED NULL DEFAULT 0');
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('supervisores');

        if ($table->hasColumn('estagiarios_count')) {
            $this->execute('ALTER TABLE `supervisores` MODIFY `estagiarios_count` INT NULL DEFAULT 0');
        }
    }
}
