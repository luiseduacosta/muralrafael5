<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\VisitasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class VisitasTablePolicy implements BeforePolicyInterface
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
    return new Result(false, 'Erro: visitas index policy not authorized');
  }

  public function canView()
  {
    return new Result(false, 'Erro: visitas view policy not authorized');
  }
  
  public function canEdit()
  {
    return new Result(false, 'Erro: visitas edit policy not authorized');
  }

  public function canAdd()
  {
    return new Result(false, 'Erro: visitas add policy not authorized');
  }

  public function canDelete()
  {
    return new Result(false, 'Erro: visitas delete policy not authorized');
  }
}