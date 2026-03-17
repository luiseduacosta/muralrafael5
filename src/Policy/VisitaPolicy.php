<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Visita;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class VisitaPolicy implements BeforePolicyInterface
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

            if (
                $user_data
                && (
                    $user_data['administrador_id']
                    || $user_data['professor_id']
                )
            ) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Visita $visita
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $user, Visita $visita): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Visita $visita
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $user, Visita $visita): Result
    {
        return new Result(false, 'Erro: visita edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Visita $visita
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $user, Visita $visita): Result
    {
        return new Result(false, 'Erro: visita delete policy not authorized');
    }
}
