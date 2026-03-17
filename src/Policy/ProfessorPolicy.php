<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Professor;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class ProfessorPolicy implements BeforePolicyInterface
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
     * @return \Authorization\Policy\Result
     */
    public function canAdd(): Result
    {
        return new Result(true);
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canView(): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Professor $professorData
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $userSession, Professor $professorData): Result
    {
        return $this->sameUser($userSession, $professorData)
            ? new Result(true)
            : new Result(false, 'Erro: professor edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Professor $professorData
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $userSession, Professor $professorData): Result
    {
        return new Result(false, 'Erro: professor delete policy not allowed');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Professor $professorData
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, Professor $professorData): bool
    {
        return $userSession->id === $professorData->user_id;
    }
}
