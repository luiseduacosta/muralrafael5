<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Inscricao;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class InscricaoPolicy implements BeforePolicyInterface
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
  
  public function canView(IdentityInterface $userSession, Inscricao $inscricaoData)
  {
    if ($this->sameUser($userSession, $inscricaoData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: inscricoes view policy not authorized');
    }
  }
  
  public function canEdit(IdentityInterface $userSession, Inscricao $inscricaoData)
  {
    if ($this->sameUser($userSession, $inscricaoData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: inscricoes edit policy not authorized');
    }
  }
  
  public function canDelete(IdentityInterface $userSession, Inscricao $inscricaoData)
  {
    return new Result(false, 'Erro: inscricoes delete policy not allowed');
  }

  
  protected function sameUser(IdentityInterface $userSession, Inscricao $inscricaoData)
  {
    return ($userSession->id == $inscricaoData->aluno->user_id);
  }
  
}