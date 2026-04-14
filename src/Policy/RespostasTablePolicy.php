<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\RespostasTable;
use Authorization\IdentityInterface;

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
    public function canIndex(?IdentityInterface $user, \Cake\ORM\Table $respostas)
    {
        return isset($user);
    }
}
