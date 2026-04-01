<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\AlunosTable;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;
use Cake\ORM\Query;

final class AlunosTablePolicy implements BeforePolicyInterface
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
                    $user_data['administrador_id'] || $user_data['professor_id']
                )
            ) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Table\AlunosTable $alunosTable
     * @return \Authorization\Policy\Result
     */
    public function canIndex(IdentityInterface $userSession, AlunosTable $alunosTable): Result
    {
        return new Result(false, 'Erro: alunos index policy not authorized');
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function scopeIndex(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Alunos.user_id' => $user->getIdentifier()]);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Table\AlunosTable $alunosTable
     * @return \Authorization\Policy\Result
     */
    public function canAdd(IdentityInterface $userSession, AlunosTable $alunosTable): Result
    {
        $alunocadastrado = $alunosTable->find()->where(['user_id' => $userSession->id]);

        return $alunocadastrado->count() > 0
            ? new Result(false, 'Erro: alunos add policy not authorized')
            : new Result(true);
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canBusca(): Result
    {
        return new Result(false, 'Erro: alunos busca policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canPlanilhacress(): Result
    {
        return new Result(false, 'Erro: alunos planilha cress policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canPlanilhaseguro(): Result
    {
        return new Result(false, 'Erro: alunos planilha seguro policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canCargahoraria(): Result
    {
        return new Result(false, 'Erro: alunos cargahoraria policy not authorized');
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canDeclaracaoperiodo(): Result
    {
        return new Result(true);
    }
}
