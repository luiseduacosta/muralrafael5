<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Makes administradores.user_id nullable (the Initial snapshot defined it as
 * NOT NULL; production has it nullable).
 */
class AlterAdministradoresUserId extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $table = $this->table('administradores');

        if ($table->hasColumn('user_id')) {
            $table->changeColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ]);
            $table->update();
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('administradores');

        if ($table->hasColumn('user_id')) {
            $table->changeColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ]);
            $table->update();
        }
    }
}
