<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Creates the docente_disponibilidades table (legacy availability table for
 * planejamento, kept for schema parity with production). Not used by the
 * application code.
 */
class CreateDocenteDisponibilidades extends BaseMigration
{
    public bool $autoId = false;

    /**
     * @return void
     */
    public function up(): void
    {
        if ($this->hasTable('docente_disponibilidades')) {
            return;
        }

        $this->table('docente_disponibilidades')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('configuraplanejamento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('disponivel', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('motivo', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index(['docente_id', 'configuraplanejamento_id'])
                    ->setName('docente_id_2')
                    ->setType('unique'),
            )
            ->addIndex(
                $this->index('docente_id')
                    ->setName('docente_id'),
            )
            ->addIndex(
                $this->index('configuraplanejamento_id')
                    ->setName('configuraplanejamento_id'),
            )
            ->create();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->table('docente_disponibilidades')->drop()->save();
    }
}
