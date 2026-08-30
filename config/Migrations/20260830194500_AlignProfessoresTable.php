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

        if ($table->hasColumn('codigo_telefone')) {
            $table->changeColumn('codigo_telefone', 'string', [
                'default' => '21',
                'limit' => 2,
                'null' => true,
            ]);
        }

        if ($table->hasColumn('codigo_celular')) {
            $table->changeColumn('codigo_celular', 'string', [
                'default' => '21',
                'limit' => 2,
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
    }

    /**
     * @return void
     */
    public function down(): void
    {
        // No-op for safety when aligning with production schema
    }
}
