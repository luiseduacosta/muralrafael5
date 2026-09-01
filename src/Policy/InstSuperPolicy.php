<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\InstSuper;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class InstSuperPolicy implements BeforePolicyInterface
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
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\InstSuper $instSuper
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $user, InstSuper $instSuper): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\InstSuper $instSuper
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $user, InstSuper $instSuper): Result
    {
        return new Result(false, 'Erro: inst_super edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\InstSuper $instSuper
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $user, InstSuper $instSuper): Result
    {
        return new Result(false, 'Erro: inst_super delete policy not authorized');
    }
}
