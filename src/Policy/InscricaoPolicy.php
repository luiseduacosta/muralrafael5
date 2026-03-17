<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Inscricao;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class InscricaoPolicy implements BeforePolicyInterface
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
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Inscricao $inscricaoData
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $userSession, Inscricao $inscricaoData): Result
    {
        return $this->sameUser($userSession, $inscricaoData)
            ? new Result(true)
            : new Result(false, 'Erro: inscricoes view policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Inscricao $inscricaoData
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $userSession, Inscricao $inscricaoData): Result
    {
        return $this->sameUser($userSession, $inscricaoData)
            ? new Result(true)
            : new Result(false, 'Erro: inscricoes edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Inscricao $inscricaoData
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $userSession, Inscricao $inscricaoData): Result
    {
        return new Result(false, 'Erro: inscricoes delete policy not allowed');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Inscricao $inscricaoData
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, Inscricao $inscricaoData): bool
    {
        return $userSession->id === $inscricaoData->aluno->user_id;
    }
}
