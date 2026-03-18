<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Estagiario;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class EstagiarioPolicy implements BeforePolicyInterface
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
     * @param \Cake\ORM\Table $table
     * @return \Authorization\Policy\Result
     */
    public function canIndex(IdentityInterface $userSession): Result
    {
        return new Result(true);
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canAdd(): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $userSession, Estagiario $estagiarioData): Result
    {
        // Supervisor pode ver estagiarios
        if ($userSession->getOriginalData()['categoria'] == '4') {
            if ($estagiarioData->supervisor_id == $userSession->getOriginalData()['supervisor_id']) {
                return new Result(true);
            }
        }

        return $this->isProfessorOwned($userSession, $estagiarioData) || $this->sameUser($userSession, $estagiarioData)
            ? new Result(true)
            : new Result(false, 'Erro: estagiario view policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $userSession, Estagiario $estagiarioData): Result
    {
        return $this->isProfessorOwned($userSession, $estagiarioData) || $this->sameUser($userSession, $estagiarioData)
            ? new Result(true)
            : new Result(false, 'Erro: estagiario edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canLancanota(IdentityInterface $userSession): Result
    {
        $user_data = $userSession->getOriginalData();
        if ($user_data['professor_id']) {
            return new Result(true);
        }

        return new Result(false, 'Erro: lancanota policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $userSession, Estagiario $estagiarioData): Result
    {
        return new Result(false, 'Erro: estagiario delete policy not allowed');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canTermoCompromisso(IdentityInterface $userSession, Estagiario $estagiarioData): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return \Authorization\Policy\Result
     */
    public function canTermoCompromissopdf(IdentityInterface $userSession, Estagiario $estagiarioData): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return bool
     */
    protected function isProfessorOwned(IdentityInterface $userSession, Estagiario $estagiarioData): bool
    {
        $user_data = $userSession->getOriginalData();

        return isset($user_data['professor_id']) && $user_data['professor_id'] === $estagiarioData->professor_id;
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Estagiario $estagiarioData
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, Estagiario $estagiarioData): bool
    {
        return $userSession->id === $estagiarioData->aluno->user_id;
    }
}
