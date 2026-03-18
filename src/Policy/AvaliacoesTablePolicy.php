<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class AvaliacoesTablePolicy implements BeforePolicyInterface
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
    public function canIndex(IdentityInterface $userSession): Result
    {
        $user_data = $userSession->getOriginalData();
        // Everyone can see the index (filtered by role in controller)
        if (in_array($user_data['categoria'], ['1', '2', '3', '4'])) {
            return new Result(true);
        }

        return new Result(false);
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canAdd(): Result
    {
        return new Result(true);
    }
}
