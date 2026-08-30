<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Table;

/**
 * RespostasTable policy
 */
class RespostasTablePolicy
{
    /**
     * Check if $user can index Respostas
     *
     * @param \Authorization\IdentityInterface|null $user The user.
     * @param \Cake\ORM\Table $respostas
     * @return bool
     */
    public function canIndex(?IdentityInterface $user, Table $respostas): bool
    {
        return isset($user);
    }
}
