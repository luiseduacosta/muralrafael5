<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Repairs and aligns the folhadeatividades table with the production (ess_apps) schema.
 *
 * The production table may lose its primary key and AUTO_INCREMENT on id in live DBs.
 * This migration restores primary key and auto increment if absent, and coerces column types to live MySQL definitions:
 *   - id            INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
 *   - estagiario_id INT(11) NOT NULL
 *   - dia           DATE NOT NULL
 *   - inicio        TIME NOT NULL
 *   - final         TIME NOT NULL
 *   - horario       TIME STORED GENERATED (timediff(`final`,`inicio`))
 *   - atividade     VARCHAR(100) NOT NULL
 */
class AlignFolhadeatividadesTable extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('folhadeatividades');

        if (!$table->hasPrimaryKey(['id'])) {
            $this->execute(
                'ALTER TABLE `folhadeatividades` ADD PRIMARY KEY (`id`), '
                . 'MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT',
            );
        }

        if ($table->hasColumn('estagiario_id')) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `estagiario_id` INT(11) NOT NULL');
        }

        if ($table->hasColumn('dia')) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `dia` DATE NOT NULL');
        }

        if ($table->hasColumn('inicio')) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `inicio` TIME NOT NULL');
        }

        if ($table->hasColumn('final')) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `final` TIME NOT NULL');
        }

        if ($table->hasColumn('atividade')) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `atividade` VARCHAR(100) NOT NULL');
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('folhadeatividades');

        if ($table->hasPrimaryKey(['id'])) {
            $this->execute('ALTER TABLE `folhadeatividades` MODIFY `id` INT(11) NOT NULL, DROP PRIMARY KEY');
        }
    }
}
