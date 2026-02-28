<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class UserPolicy implements BeforePolicyInterface
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
  
  public function canView(IdentityInterface $userSession, User $userData)
  {
    if ($this->sameUser($userSession, $userData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: user view policy not authorized');
    }
  }
  
  public function canEdit(IdentityInterface $userSession, User $userData)
  {
    if ($this->sameUser($userSession, $userData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: user edit policy not authorized');
    }
  }
  public function canEditpassword(IdentityInterface $userSession, User $userData)
  {
    if ($this->sameUser($userSession, $userData)) {
      return new Result(true);
    } else {
      return new Result(false, 'Erro: user edit policy not authorized');
    }
  }
  
  public function canDelete(IdentityInterface $userSession, User $userData)
  {
    return new Result(false, 'Erro: user delete policy not allowed');
  }

  
  protected function sameUser(IdentityInterface $userSession, User $userData)
  {
    return ($userSession->id == $userData->id);
  }
  
}