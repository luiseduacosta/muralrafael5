<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\InscricoesTable;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

final class InscricoesTablePolicy implements BeforePolicyInterface
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
     * @param \App\Model\Table\InscricoesTable $inscricoesTable
     * @return \Authorization\Policy\Result
     */
    public function canIndex(IdentityInterface $userSession, InscricoesTable $inscricoesTable): Result
    {
        return new Result(true);
    }

    /**
     * @param \Authorization\IdentityInterface $user
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function scopeIndex(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Inscricoes.user_id' => $user->getIdentifier()]);
    }

    /**
     * @param \Authorization\IdentityInterface $userSession
     * @param \App\Model\Table\InscricoesTable $inscricoesTable
     * @return \Authorization\Policy\Result
     */
    public function canAdd(IdentityInterface $userSession, InscricoesTable $inscricoesTable): Result
    {
        $alunosTable = TableRegistry::getTableLocator()->get('Alunos');
        $alunocadastrado = $alunosTable->find()->where(['user_id' => $userSession->id]);

        return $alunocadastrado->count() > 0
            ? new Result(false, 'Erro: inscricoes canAdd policy not authorized')
            : new Result(true);
    }

    /**
     * @return \Authorization\Policy\Result
     */
    public function canBusca(): Result
    {
        return new Result(false, 'Erro: inscricoes busca policy not authorized');
    }
}
