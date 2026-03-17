<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class UserPolicy implements BeforePolicyInterface
{
    /**
     * @param \Authorization\IdentityInterface|null $identity
     * @param mixed $resource
     * @param string $action
     * @return \Authorization\Policy\ResultInterface|bool|null
     */
    public function before(?IdentityInterface $identity, mixed $resource, string $action): ResultInterface|bool|null
    {
        if ($identity) {
            $user_data = $identity->getOriginalData();

            if ($user_data && $user_data['administrador_id']) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\User $userData
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $userSession, User $userData): Result
    {
        return $this->sameUser($userSession, $userData)
            ? new Result(true)
            : new Result(false, 'Erro: user view policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\User $userData
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $userSession, User $userData): Result
    {
        return $this->sameUser($userSession, $userData)
            ? new Result(true)
            : new Result(false, 'Erro: user edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\User $userData
     * @return \Authorization\Policy\Result
     */
    public function canEditpassword(IdentityInterface $userSession, User $userData): Result
    {
        return $this->sameUser($userSession, $userData)
            ? new Result(true)
            : new Result(false, 'Erro: user edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\User $userData
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $userSession, User $userData): Result
    {
        return new Result(false, 'Erro: user delete policy not allowed');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\User $userData
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, User $userData): bool
    {
        return $userSession->id === $userData->id;
    }
}
