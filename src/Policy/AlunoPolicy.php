<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Aluno;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

final class AlunoPolicy implements BeforePolicyInterface
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
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return \Authorization\Policy\Result
     */
    public function canView(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        // Supervisor pode ver alunos estagiarios
        if ($userSession->getOriginalData()['categoria'] == '4') {
            if ($alunoData->estagiarios->supervisores->id == $userSession->getOriginalData()['supervisor_id']) {
                return new Result(true);
            }
        }

        // Professor pode ver alunos estagiarios
        if ($userSession->getOriginalData()['categoria'] == '3') {
            if ($alunoData->estagiarios->professores->id == $userSession->getOriginalData()['professor_id']) {
                return new Result(true);
            }
        }

        return $this->sameUser($userSession, $alunoData)
            ? new Result(true)
            : new Result(false, 'Erro: aluno view policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return \Authorization\Policy\Result
     */
    public function canEdit(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        return $this->sameUser($userSession, $alunoData)
            ? new Result(true)
            : new Result(false, 'Erro: aluno edit policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return \Authorization\Policy\Result
     */
    public function canDelete(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        return new Result(false, 'Erro: aluno delete policy not allowed');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return \Authorization\Policy\Result
     */
    public function canDeclaracaoperiodo(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        return $this->sameUser($userSession, $alunoData)
            ? new Result(true)
            : new Result(false, 'Erro: aluno declaracao periodo policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return \Authorization\Policy\Result
     */
    public function canDeclaracaoperiodopdf(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        return $this->sameUser($userSession, $alunoData)
            ? new Result(true)
            : new Result(false, 'Erro: aluno declaracao periodo policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Entity\Aluno $alunoData
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, Aluno $alunoData): bool
    {
        return $userSession->id === $alunoData->user_id;
    }
}
