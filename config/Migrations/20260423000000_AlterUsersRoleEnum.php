<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Recreated on 2026-08-30 because the file was lost from the repository.
 *
 * Changes users.role and users.categoria to the ENUM values used in
 * production (categoria was left out of the conversion script and handled
 * here later, as noted in update_tables.sql).
 */
class AlterUsersRoleEnum extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE `users` MODIFY `role` ENUM('admin','supervisor','professor','aluno') DEFAULT 'aluno'",
        );
        $this->execute("ALTER TABLE `users` MODIFY `categoria` ENUM('1','2','3','4') NOT NULL DEFAULT '2'");
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->execute("ALTER TABLE `users` MODIFY `role` VARCHAR(255) NOT NULL DEFAULT 'aluno'");
        $this->execute("ALTER TABLE `users` MODIFY `categoria` VARCHAR(255) NOT NULL DEFAULT '2'");
    }
}
