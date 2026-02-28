<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\InscricoesTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class InscricoesTablePolicy implements BeforePolicyInterface
{
  
  public function before(?IdentityInterface $identity, mixed $resource, string $action): ResultInterface|bool|null
  {
    if ($identity) {
      $user_data = $identity->getOriginalData();
      if ($user_data and ( $user_data['administrador_id'] || $user_data['professor_id'] || $user_data['supervisor_id'])) {
        return true;
      }
    }
    return null;
  }

  public function canIndex(IdentityInterface $userSession, InscricoesTable $alunosTable)
  {
    return new Result(false, 'Erro: inscricoes busca policy not authorized');
  }
  
  public function scopeIndex($user, $query)
  {
    return $query->where(['Inscricoes.user_id' => $user->getIdentifier()]);
  }

  public function canAdd(IdentityInterface $userSession, InscricoesTable $inscricoesTable)
  {
    $alunocadastrado = $this->fetchTable("Alunos")->find()->where(['user_id' => $userSession->id]);

    if ($alunocadastrado->count() > 0) {
        return new Result(false, 'Erro: inscricoes canAdd policy not authorized');
    } else {
      return new Result(true);
    }
    
  }
  
  public function canBusca()
  {
    return new Result(false, 'Erro: inscricoes busca policy not authorized');
  }

}