<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Professor;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class ProfessorPolicy implements BeforePolicyInterface
{
  
  public function before(?IdentityInterface $identity, mixed $resource, string $action): ResultInterface|bool|null
  {
    if ($identity) {
      $user_data = $identity->getOriginalData();
      if ($user_data and $user_data['administrador_id']) {
        return true;
      }
    }
    return null;
  }
  
  public function canAdd()
  {
      return new Result(true);
  }
  
  public function canView()
  {
    return new Result(true);
  }
  
  public function canEdit(IdentityInterface $userSession, Professor $professorData)
  {
    if ($this->sameUser($userSession, $professorData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: professor edit policy not authorized');
    }
  }
  
  public function canDelete(IdentityInterface $userSession, Professor $professorData)
  {
    return new Result(false, 'Erro: professor delete policy not allowed');
  }

  
  protected function sameUser(IdentityInterface $userSession, Professor $professorData)
  {
    return ($userSession->id == $professorData->user_id);
  }
  
}