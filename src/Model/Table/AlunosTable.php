<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Alunos Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\InscricoesTable&\Cake\ORM\Association\HasMany $Inscricoes
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TurnosTable&\Cake\ORM\Association\BelongsTo $Turnos
 * @method \App\Model\Entity\Aluno newEmptyEntity()
 * @method \App\Model\Entity\Aluno newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Aluno[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Aluno get($primaryKey, $options = [])
 * @method \App\Model\Entity\Aluno findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Aluno patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Aluno[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Aluno|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aluno saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aluno[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aluno[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aluno[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aluno[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AlunosTable extends Table
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

        $this->setTable('alunos');
        $this->setAlias('Alunos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasMany('Inscricoes', [
            'foreignKey' => 'aluno_id',
        ]);
        $this->hasMany('Estagiarios', [
            'foreignKey' => 'aluno_id',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);

        $this->belongsTo('Turnos', [
            'foreignKey' => 'turno_id',
            'propertyName' => 'Turno',
            'joinType' => 'LEFT',
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
            ->scalar('nome')
            ->maxLength('nome', 50)
            ->notEmptyString('nome');

        $validator
            ->scalar('nomesocial')
            ->maxLength('nomesocial', 50)
            ->allowEmptyString('nomesocial');

        $validator
            ->integer('registro')
            ->notEmptyString('registro')
            ->add('registro', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('ingresso')
            ->requirePresence('ingresso', 'create')
            ->notEmptyString('ingresso', 'create')
            ->maxLength('ingresso', 6)
            ->regex('ingresso', '/^(19|20)[0-9]{2}-[1-2]{1}$/', 'Ano e semestre de ingresso inválido', 'create')
            ->allowEmptyString('ingresso', 'create');

        $validator
            ->integer('turno_id')
            ->allowEmptyString('turno_id');

        $validator
            ->notEmptyString('codigo_telefone');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 15)
            ->regex('telefone', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Telefone inválido')
            ->allowEmptyString('telefone');

        $validator
            ->notEmptyString('codigo_celular');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 15)
            ->regex('celular', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Telefone inválido')
            ->allowEmptyString('celular');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 14)
            ->regex('cpf', '/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/', 'CPF inválido')
            ->allowEmptyString('cpf');

        $validator
            ->scalar('identidade')
            ->maxLength('identidade', 15)
            ->allowEmptyString('identidade');

        $validator
            ->scalar('orgao')
            ->maxLength('orgao', 30)
            ->allowEmptyString('orgao');

        $validator
            ->date('nascimento')
            ->allowEmptyDate('nascimento');

        $validator
            ->scalar('endereco')
            ->maxLength('endereco', 50)
            ->allowEmptyString('endereco');

        $validator
            ->scalar('cep')
            ->maxLength('cep', 9)
            ->regex('cep', '/^[0-9]{5}-[0-9]{3}$/', 'CEP inválido')
            ->allowEmptyString('cep');

        $validator
            ->scalar('municipio')
            ->maxLength('municipio', 30)
            ->allowEmptyString('municipio');

        $validator
            ->scalar('bairro')
            ->maxLength('bairro', 30)
            ->allowEmptyString('bairro');

        $validator
            ->scalar('observacoes')
            ->maxLength('observacoes', 250)
            ->allowEmptyString('observacoes');

        $validator
            ->integer('estagiario_count')
            ->allowEmptyString('estagiario_count');

        $validator
            ->integer('inscricao_count')
            ->allowEmptyString('inscricao_count');

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
        $rules->add($rules->isUnique(['registro']), ['errorField' => 'registro']);

        return $rules;
    }
}
