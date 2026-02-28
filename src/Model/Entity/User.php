<?php
declare(strict_types=1);

namespace App\Model\Entity;

use ArrayAccess;
use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Authentication\IdentityInterface as AuthenticationIdentity;
use Authorization\AuthorizationService;
use Authorization\IdentityInterface as AuthorizationIdentity;
use Authorization\Policy\ResultInterface;
use Cake\ORM\TableRegistry;

/**
 * User Entity
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $password
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class User extends Entity implements AuthorizationIdentity, AuthenticationIdentity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'email' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected array $_hidden = ['password'];

    // Automatically hash passwords when they are changed.
    protected function _setPassword(string $password)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($password);
    }
    
    /**
     * Authentication\IdentityInterface method
     */
    public function getIdentifier(): array|string|int|null
    {
        return $this->id;
    }

    /**
     * Authentication\IdentityInterface method
     */
    public function getOriginalData(): ArrayAccess|array
    {
        $alunos = TableRegistry::getTableLocator()->get('Alunos');
        $aluno = $alunos->find('all', ['conditions' => ['Alunos.user_id' => $this->id] ])->first();
        $this->aluno_id = false;
        if (!empty($aluno)) $this->aluno_id = $aluno->id;

        $professores = TableRegistry::getTableLocator()->get('Professores');
        $professor = $professores->find('all', ['conditions' => ['Professores.user_id' => $this->id] ])->first();
        $this->professor_id = false;
        if (!empty($professor)) $this->professor_id = $professor->id;

        $supervisores = TableRegistry::getTableLocator()->get('Supervisores');
        $supervisor = $supervisores->find('all', ['conditions' => ['Supervisores.user_id' => $this->id] ])->first();
        $this->supervisor_id = false;
        if (!empty($supervisor)) $this->supervisor_id = $supervisor->id;

        $administradores = TableRegistry::getTableLocator()->get('Administradores');
        $administrador = $administradores->find('all', ['conditions' => ['Administradores.user_id' => $this->id] ])->first();
        $this->administrador_id = false;
        if (!empty($administrador)) $this->administrador_id = $administrador->id;
        
        return $this;
    }

    /**
     * Authorization\IdentityInterface method
     */
    public function can(string $action, mixed $resource): bool
    {
        return $this->authorization->can($this, $action, $resource);
    }

     /**
     * Authorization\IdentityInterface method
     */   
    public function canResult(string $action, mixed $resource): ResultInterface
    {
        return $this->authorization->canResult($this, $action, $resource);
    }
    
    /**
     * Authorization\IdentityInterface method
     */
    public function applyScope(string $action, mixed $resource, mixed ...$optionalArgs): mixed
    {
        return $this->authorization->applyScope($this, $action, $resource, ...$optionalArgs);
    }

    /**
     * Setter to be used by the middleware.
     */
    public function setAuthorization(AuthorizationService $service)
    {
        $this->authorization = $service;

        return $this;
    }

}
