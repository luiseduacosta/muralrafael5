<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Supervisores Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsToMany $Instituicoes
 *
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
        /*
        $this->hasOne('Users', [
            'foreignKey' => 'supervisor_id',
        ]);  */   
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
            ->maxLength('cpf', 12)
            ->notEmptyString('cpf');

        $validator
            ->scalar('endereco')
            ->maxLength('endereco', 100)
            ->requirePresence('endereco', 'create')
            ->notEmptyString('endereco');

        $validator
            ->scalar('bairro')
            ->maxLength('bairro', 30)
            ->requirePresence('bairro', 'create')
            ->notEmptyString('bairro');

        $validator
            ->scalar('municipio')
            ->maxLength('municipio', 30)
            ->requirePresence('municipio', 'create')
            ->notEmptyString('municipio');

        $validator
            ->scalar('cep')
            ->maxLength('cep', 9)
            ->requirePresence('cep', 'create')
            ->notEmptyString('cep');

        $validator
            ->scalar('codigo_tel')
            ->maxLength('codigo_tel', 2)
            ->notEmptyString('codigo_tel');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 9)
            ->notEmptyString('telefone');

        $validator
            ->scalar('codigo_cel')
            ->maxLength('codigo_cel', 2)
            ->notEmptyString('codigo_cel');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 10)
            ->notEmptyString('celular');

        $validator
            ->scalar('escola')
            ->maxLength('escola', 70)
            ->requirePresence('escola', 'create')
            ->notEmptyString('escola');

        $validator
            ->scalar('ano_formatura')
            ->maxLength('ano_formatura', 4)
            ->requirePresence('ano_formatura', 'create')
            ->notEmptyString('ano_formatura');

        $validator
            ->integer('cress')
            ->allowEmptyString('cress');

        $validator
            ->notEmptyString('regiao');

        $validator
            ->scalar('outros_estudos')
            ->maxLength('outros_estudos', 100)
            ->allowEmptyString('outros_estudos');

        $validator
            ->scalar('area_curso')
            ->maxLength('area_curso', 40)
            ->allowEmptyString('area_curso');

        $validator
            ->scalar('ano_curso')
            ->maxLength('ano_curso', 4)
            ->allowEmptyString('ano_curso');

        $validator
            ->scalar('cargo')
            ->maxLength('cargo', 25)
            ->allowEmptyString('cargo');

        $validator
            ->integer('num_inscricao')
            ->allowEmptyString('num_inscricao');

        $validator
            ->scalar('curso_turma')
            ->maxLength('curso_turma', 1)
            ->allowEmptyString('curso_turma');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        return $validator;
    }
}
