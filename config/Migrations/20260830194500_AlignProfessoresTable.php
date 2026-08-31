<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AlignProfessoresTable extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('professores');

        if ($table->hasColumn('nome')) {
            $table->changeColumn('nome', 'string', [
                'limit' => 200,
                'null' => false,
            ]);
        }

        if ($table->hasColumn('siape')) {
            $table->changeColumn('siape', 'string', [
                'limit' => 8,
                'null' => true,
            ]);
        }

        if (!$table->hasColumn('tipocargo')) {
            $table->addColumn('tipocargo', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ]);
        }

        if (!$table->hasColumn('status')) {
            $table->addColumn('status', 'string', [
                'default' => 'ativo',
                'limit' => 10,
                'null' => false,
            ]);
        }

        if (!$table->hasColumn('estagiarios_count')) {
            $table->addColumn('estagiarios_count', 'integer', [
                'default' => 0,
                'null' => true,
                'signed' => false,
            ]);
        }

        if (!$table->hasColumn('created')) {
            $table->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
            ]);
        }

        if (!$table->hasColumn('modified')) {
            $table->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
            ]);
        }

        if ($table->hasColumn('usser_id')) {
            $table->removeColumn('usser_id');
        }

        $table->update();

        // Exact column types present in production, applied with raw SQL so
        // they match the live schema (char widths, unsigned integers and the
        // column order of user_id/estagiarios_count before observacoes).
        if ($table->hasColumn('codigo_telefone')) {
            $this->execute("ALTER TABLE `professores` MODIFY `codigo_telefone` CHAR(2) NULL DEFAULT '21'");
        }

        if ($table->hasColumn('codigo_celular')) {
            $this->execute("ALTER TABLE `professores` MODIFY `codigo_celular` CHAR(2) NULL DEFAULT '21'");
        }

        if ($table->hasColumn('user_id') && $table->hasColumn('estagiarios_count')) {
            $this->execute('ALTER TABLE `professores` MODIFY `user_id` INT UNSIGNED NULL AFTER `status`');
            $this->execute(
                'ALTER TABLE `professores` MODIFY `estagiarios_count` INT UNSIGNED NULL DEFAULT 0 AFTER `user_id`',
            );
        } else {
            if ($table->hasColumn('user_id')) {
                $this->execute('ALTER TABLE `professores` MODIFY `user_id` INT UNSIGNED NULL');
            }

            if ($table->hasColumn('estagiarios_count')) {
                $this->execute('ALTER TABLE `professores` MODIFY `estagiarios_count` INT UNSIGNED NULL DEFAULT 0');
            }
        }

        if ($table->hasColumn('modified')) {
            $this->execute(
                'ALTER TABLE `professores` MODIFY `modified` DATETIME NOT NULL DEFAULT current_timestamp() '
                . 'ON UPDATE current_timestamp()',
            );
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        // No-op for safety when aligning with production schema
    }
}
