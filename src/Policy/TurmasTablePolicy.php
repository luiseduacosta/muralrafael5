<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\TurmasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class TurmasTablePolicy implements BeforePolicyInterface
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

  public function canIndex()
  {
    return new Result(false, 'Erro: turmas index policy not authorized');
  }

  public function canView()
  {
    return new Result(false, 'Erro: turmas view policy not authorized');
  }
  
  public function canEdit()
  {
    return new Result(false, 'Erro: turmas edit policy not authorized');
  }

  public function canAdd()
  {
    return new Result(false, 'Erro: turmas add policy not authorized');
  }

  public function canDelete()
  {
    return new Result(false, 'Erro: turmas delete policy not authorized');
  }
}