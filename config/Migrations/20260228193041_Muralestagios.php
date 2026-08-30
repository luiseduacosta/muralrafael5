<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Legacy migration from the original app (2026-02-28), recreated on 2026-08-30
 * because the file was lost from the repository.
 *
 * The mural_estagios table was recreated by the 20260416052454 Initial
 * snapshot, so this migration is intentionally a no-op.
 */
class Muralestagios extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
    }

    /**
     * @return void
     */
    public function down(): void
    {
    }
}
