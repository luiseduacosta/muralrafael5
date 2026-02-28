<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\InstituicoesTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;


class InstituicoesTablePolicy implements BeforePolicyInterface
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

  public function canIndex()
  {
    return new Result(true);
  }

  public function canView()
  {
    return new Result(true);
  }
  
  public function canEdit()
  {
    return new Result(false, 'Erro: instituicoes edit policy not authorized');
  }

  public function canAdd()
  {
    return new Result(false, 'Erro: instituicoes add policy not authorized');
  }

  public function canDelete()
  {
    return new Result(false, 'Erro: instituicoes delete policy not authorized');
  }
}