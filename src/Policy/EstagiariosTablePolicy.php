<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\EstagiariosTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\BeforePolicyInterface;
use Authorization\Policy\ResultInterface;

class EstagiariosTablePolicy implements BeforePolicyInterface
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

  public function canIndex(IdentityInterface $userSession, EstagiariosTable $estagiariosTable)
  {
    return new Result(false, 'Erro: estagiarios index policy not authorized');
  }
  
  public function scopeIndex($user, $query)
  {
    return $query->where(['Estagiarios.aluno_id' => $user->aluno_id ]);
  }
  
  public function canBusca()
  {
    return new Result(false, 'Erro: estagiarios busca policy not authorized');
  }

}