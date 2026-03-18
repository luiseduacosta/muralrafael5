<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Avaliacao;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class AvaliacaoPolicy implements BeforePolicyInterface
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
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Avaliacao $avaliacao
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $user, Avaliacao $avaliacao): Result
    {
        $user_data = $user->getOriginalData();

        // Student can only view their own
        if ($user_data['aluno_id']) {
            return new Result($user_data['aluno_id'] == $avaliacao->estagiario->aluno_id);
        }

        // Professor can view evaluations for their assigned students
        if ($user_data['professor_id']) {
            return new Result($user_data['professor_id'] == $avaliacao->estagiario->professor_id);
        }

        return new Result(false);
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Avaliacao $avaliacao
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $user, Avaliacao $avaliacao): Result
    {
        return new Result(false, 'Erro: avaliacao edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \App\Model\Entity\Avaliacao $avaliacao
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $user, Avaliacao $avaliacao): Result
    {
        return new Result(false, 'Erro: avaliacao delete policy not authorized');
    }
}
