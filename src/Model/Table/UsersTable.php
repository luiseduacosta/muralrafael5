<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\AlunosTable&\Cake\ORM\Association\BelongsTo $Alunos
 * @property \App\Model\Table\SupervisoresTable&\Cake\ORM\Association\BelongsTo $Supervisores
 * @property \App\Model\Table\ProfessoresTable&\Cake\ORM\Association\BelongsTo $Professores
 * @property \App\Model\Table\AdministradoresTable&\Cake\ORM\Association\BelongsTo $Administradores
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setAlias('Users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                    'atualizado_em' => 'always',
                ],
            ],
        ]);

        $this->hasOne('Administradores', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('Alunos', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('Professores', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasOne('Supervisores', [
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('email')
            ->maxLength('email', 50)
            ->email('email')
            ->notEmptyString('email', 'Erro: Email vazio');

        $validator
            ->scalar('password')
            ->maxLength('password', 80)
            ->notEmptyString('password', 'Erro: senha vazia');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 128)
            ->allowEmptyString('nome');

        $validator
            ->scalar('role')
            ->inList('role', ['admin', 'supervisor', 'professor', 'aluno'], 'Erro: role inválida')
            ->allowEmptyString('role');

        $validator
            ->scalar('categoria')
            ->inList('categoria', ['1', '2', '3', '4'], 'Erro: categoria inválida')
            ->notEmptyString('categoria', 'Erro: categoria vazia');

        $validator
            ->integer('identificacao')
            ->allowEmptyString('identificacao');

        $validator
            ->integer('entidade_id')
            ->allowEmptyString('entidade_id');

        $validator
            ->boolean('ativo')
            ->allowEmptyString('ativo');

        $validator
            ->integer('aluno_id')
            ->allowEmptyString('aluno_id');

        $validator
            ->integer('supervisor_id')
            ->allowEmptyString('supervisor_id');

        $validator
            ->integer('professor_id')
            ->allowEmptyString('professor_id');

        $validator
            ->dateTime('criado_em')
            ->allowEmptyDateTime('criado_em');

        $validator
            ->dateTime('atualizado_em')
            ->allowEmptyDateTime('atualizado_em');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
