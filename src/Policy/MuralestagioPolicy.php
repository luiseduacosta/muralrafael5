<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Muralestagio;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class MuralestagioPolicy implements BeforePolicyInterface
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
     * @param \App\Model\Entity\Muralestagio $muralestagio
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $user, Muralestagio $muralestagio): Result
    {
        return new Result(false);
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Muralestagio $muralestagio
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $user, Muralestagio $muralestagio): Result
    {
        return new Result(false, 'Erro: muralestagio edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Muralestagio $muralestagio
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $user, Muralestagio $muralestagio): Result
    {
        return new Result(false, 'Erro: muralestagio delete policy not authorized');
    }
}
