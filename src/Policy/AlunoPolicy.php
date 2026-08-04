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
                    !empty($user_data['administrador_id'])
                    || !empty($user_data['professor_id'])
                    || !empty($user_data['supervisor_id'])
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
        $userData = $userSession->getOriginalData();
        $categoria = $userData['categoria'] ?? null;
        $supervisorId = $userData['supervisor_id'] ?? null;
        $professorId = $userData['professor_id'] ?? null;

        // Supervisor pode ver alunos estagiarios
        if ($categoria == '4' && $supervisorId && !empty($alunoData->estagiarios)) {
            foreach ($alunoData->estagiarios as $estagiario) {
                if (isset($estagiario->supervisor_id) && $estagiario->supervisor_id == $supervisorId) {
                    return new Result(true);
                }
            }
        }

        // Professor pode ver alunos estagiarios
        if ($categoria == '3' && $professorId && !empty($alunoData->estagiarios)) {
            foreach ($alunoData->estagiarios as $estagiario) {
                if (isset($estagiario->professor_id) && $estagiario->professor_id == $professorId) {
                    return new Result(true);
                }
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
     * Check if the identity user owns the Aluno resource.
     *
     * Primary: user.entidade_id (unified entity ID) matches Aluno.id.
     * Fallback: user.id matches Aluno.user_id (for legacy records).
     *
     * @param \Authorization\IdentityInterface $userSession The logged-in identity (User entity).
     * @param \App\Model\Entity\Aluno $alunoData The Aluno resource being authorized.
     * @return bool
     */
    protected function sameUser(IdentityInterface $userSession, Aluno $alunoData): bool
    {
        if ($userSession->entidade_id !== null && (int)$userSession->entidade_id === (int)$alunoData->id) {
            return true;
        }

        return $userSession->id === $alunoData->user_id;
    }
}
