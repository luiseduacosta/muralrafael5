<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Supervisores Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsToMany $Instituicoes
 * @method \App\Model\Entity\Supervisor newEmptyEntity()
 * @method \App\Model\Entity\Supervisor newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Supervisor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Supervisor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Supervisor findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Supervisor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Supervisor[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Supervisor|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supervisor saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supervisor[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supervisor[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supervisor[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supervisor[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SupervisoresTable extends Table
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

        $this->setTable('supervisores');
        $this->setAlias('Supervisores');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasMany('Estagiarios', [
            'foreignKey' => 'supervisor_id',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsToMany('Instituicoes', [
            'foreignKey' => 'supervisor_id',
            'targetForeignKey' => 'instituicao_id',
            'joinTable' => 'inst_super',
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
            ->maxLength('nome', 70)
            ->notEmptyString('nome');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 14)
            ->regex('cpf', '/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/', 'CPF inválido')
            ->allowEmptyString('cpf');

        $validator
            ->scalar('endereco')
            ->maxLength('endereco', 100)
            ->allowEmptyString('endereco');

        $validator
            ->scalar('bairro')
            ->maxLength('bairro', 30)
            ->allowEmptyString('bairro');

        $validator
            ->scalar('municipio')
            ->maxLength('municipio', 30)
            ->allowEmptyString('municipio');

        $validator
            ->scalar('cep')
            ->maxLength('cep', 9)
            ->regex('cep', '/^[0-9]{5}-[0-9]{3}$/', 'CEP inválido')
            ->allowEmptyString('cep');

        $validator
            ->scalar('codigo_tel')
            ->maxLength('codigo_tel', 2)
            ->regex('codigo_tel', '/^[0-9]{2}$/', 'Código de telefone inválido')
            ->allowEmptyString('codigo_tel');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 15)
            ->regex('telefone', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Telefone inválido')
            ->allowEmptyString('telefone');

        $validator
            ->nonNegativeInteger('codigo_cel')
            ->maxLength('codigo_cel', 2)
            ->regex('codigo_cel', '/^[0-9]{2}$/', 'Código de celular inválido')
            ->allowEmptyString('codigo_cel');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 15)
            ->regex('celular', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Celular inválido')
            ->allowEmptyString('celular');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('escola')
            ->maxLength('escola', 70)
            ->allowEmptyString('escola');

        $validator
            ->nonNegativeInteger('ano_formatura')
            ->regex('ano_formatura', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('ano_formatura');

        $validator
            ->nonNegativeInteger('cress')
            ->notEmptyString('cress', null, 'create');

        $validator
            ->nonNegativeInteger('regiao')
            ->maxLength('regiao', 2)
            ->notEmptyString('regiao', null, 'create');

        $validator
            ->scalar('outros_estudos')
            ->maxLength('outros_estudos', 100)
            ->allowEmptyString('outros_estudos');

        $validator
            ->scalar('area_curso')
            ->maxLength('area_curso', 40)
            ->allowEmptyString('area_curso');

        $validator
            ->nonNegativeInteger('ano_curso')
            ->regex('ano_curso', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('ano_curso');

        $validator
            ->scalar('cargo')
            ->maxLength('cargo', 25)
            ->allowEmptyString('cargo');

        $validator
            ->nonNegativeInteger('num_inscricao')
            ->allowEmptyString('num_inscricao');

        $validator
            ->integer('estagiarios_count')
            ->allowEmptyString('estagiarios_count');

        $validator
            ->scalar('curso_turma')
            ->maxLength('curso_turma', 1)
            ->allowEmptyString('curso_turma');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->scalar('estagiarios_count')
            ->allowEmptyString('estagiarios_count');

        return $validator;
    }
}
