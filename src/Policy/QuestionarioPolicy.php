<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Questionario;
use Authorization\IdentityInterface;

/**
 * Questionario policy
 */
class QuestionarioPolicy
{
    /**
     * Check if $user can add questionario
     *
     * @param \Authorization\IdentityInterface|null $user The user.
     * @param \App\Model\Entity\questionario $questionario
     * @return bool
     */
    public function canAdd(?IdentityInterface $user, Questionario $questionario)
    {
        return $user->categoria === 1;
    }

    /**
     * Check if $user can edit questionario
     *
     * @param \Authorization\IdentityInterface|null $user The user.
     * @param \App\Model\Entity\questionario $questionario
     * @return bool
     */
    public function canEdit(?IdentityInterface $user, Questionario $questionario)
    {
        return $user->categoria === 1;
    }

    /**
     * Check if $user can delete questionario
     *
     * @param \Authorization\IdentityInterface|null $user The user.
     * @param \App\Model\Entity\questionario $questionario
     * @return bool
     */
    public function canDelete(?IdentityInterface $user, Questionario $questionario)
    {
        return $user->categoria === 1;
    }

    /**
     * Check if $user can view questionario
     *
     * @param \Authorization\IdentityInterface|null $user The user.
     * @param \App\Model\Entity\questionario $questionario
     * @return bool
     */
    public function canView(?IdentityInterface $user, Questionario $questionario)
    {
        return isset($user);
    }
}
