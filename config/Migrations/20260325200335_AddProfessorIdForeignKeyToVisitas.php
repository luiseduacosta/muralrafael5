<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddProfessorIdForeignKeyToVisitas extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('visitas');

        // Add professor_id column if it doesn't exist
        if (!$table->hasColumn('professor_id')) {
            $table->addColumn('professor_id', 'integer', [
                'null' => true,
                'after' => 'instituicao_id',
            ]);
        }

        // Add foreign key constraint for professor_id referencing professores table
        $table->addForeignKey(
            'professor_id',
            'professores',
            'id',
            [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ]
        );

        $table->update();
    }
}
