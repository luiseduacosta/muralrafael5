<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Aluno;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class AlunoPolicy implements BeforePolicyInterface
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
  
  public function canView(IdentityInterface $userSession, Aluno $alunoData)
  {
    if ($this->sameUser($userSession, $alunoData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: aluno view policy not authorized');
    }
  }
  
  public function canEdit(IdentityInterface $userSession, Aluno $alunoData)
  {
    if ($this->sameUser($userSession, $alunoData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: aluno edit policy not authorized');
    }
  }
  
  public function canDelete(IdentityInterface $userSession, Aluno $alunoData)
  {
    return new Result(false, 'Erro: aluno delete policy not allowed');
  }

  
  protected function sameUser(IdentityInterface $userSession, Aluno $alunoData)
  {
    return ($userSession->id == $alunoData->user_id);
  }
  
}