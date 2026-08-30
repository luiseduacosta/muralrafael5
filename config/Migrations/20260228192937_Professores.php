<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Legacy migration from the original app (2026-02-28), recreated on 2026-08-30
 * because the file was lost from the repository.
 *
 * The professores table must exist before AddProfessorIdForeignKeyToVisitas
 * (20260325200335) runs. The 20260416052454 Initial snapshot drops and
 * recreates it with the full definition, so only a placeholder is created
 * here.
 */
class Professores extends BaseMigration
{
    public bool $autoId = false;

    /**
     * @return void
     */
    public function up(): void
    {
        $this->table('professores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->create();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->table('professores')->drop()->save();
    }
}
