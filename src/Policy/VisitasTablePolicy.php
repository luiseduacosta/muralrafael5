<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class VisitasTablePolicy implements BeforePolicyInterface
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
                    || $user_data['supervisor_id']
                )
            ) {
                return true;
            }
        }

        return null;
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canIndex(): Result
    {
        return new Result(false, 'Erro: visitas index policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canView(): Result
    {
        return new Result(false, 'Erro: visitas view policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canEdit(): Result
    {
        return new Result(false, 'Erro: visitas edit policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canAdd(): Result
    {
        return new Result(false, 'Erro: visitas add policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canDelete(): Result
    {
        return new Result(false, 'Erro: visitas delete policy not authorized');
    }
}
