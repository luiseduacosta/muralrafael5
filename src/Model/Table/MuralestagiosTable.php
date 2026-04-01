<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Muralestagios Model
 *
 * @property \App\Model\Table\InscricoesTable&\Cake\ORM\Association\HasMany $Inscricoes
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsTo $Instituicoes
 * @method \App\Model\Entity\Muralestagio newEmptyEntity()
 * @method \App\Model\Entity\Muralestagio newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Muralestagio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Muralestagio get($primaryKey, $options = [])
 * @method \App\Model\Entity\Muralestagio findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Muralestagio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Muralestagio[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Muralestagio|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Muralestagio saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */

class MuralestagiosTable extends Table
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

        $this->setTable('mural_estagios');
        $this->setAlias('Muralestagios');
        $this->setDisplayField('instituicao');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Muralestagio');

        $this->hasMany('Inscricoes', [
            'foreignKey' => 'muralestagio_id',
        ]);

        $this->belongsTo('Instituicoes', [
            'foreignKey' => 'instituicao_id',
            'propertyName' => 'instituicao_entidade',
        ]);
    }

    /**
     * Before find callback to apply default ordering.
     *
     * @param \Cake\Event\EventInterface $event The beforeFind event.
     * @param \Cake\ORM\Query $query The query object.
     * @param \ArrayObject $options The options array.
     * @param bool $primary Whether this is a primary query or not.
     * @return \Cake\ORM\Query
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, bool $primary): Query
    {
        $query->order(['data_inscricao' => 'DESC']);

        return $query;
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
                ->integer('instituicao_id')
                ->notEmptyString('instituicao_id');

        $validator
                ->scalar('convenio')
                ->maxLength('convenio', 1)
                ->inList('convenio', ['0', '1'])
                ->notEmptyString('convenio');

        $validator
                ->nonNegativeInteger('vagas')
                ->notEmptyString('vagas');

        $validator
                ->scalar('beneficios')
                ->maxLength('beneficios', 70)
                ->allowEmptyString('beneficios');

        $validator
                ->scalar('final_de_semana')
                ->maxLength('final_de_semana', 1)
                ->inList('final_de_semana', ['0', '1', '2'])
                ->allowEmptyString('final_de_semana');

        $validator
                ->nonNegativeInteger('carga_horaria')
                ->allowEmptyString('carga_horaria');

        $validator
                ->scalar('requisitos')
                ->maxLength('requisitos', 455)
                ->allowEmptyString('requisitos');

        $validator
                ->scalar('horario')
                ->maxLength('horario', 1)
                ->allowEmptyString('horario');

        $validator
                ->scalar('local_inscricao')
                ->notEmptyString('local_inscricao');

        $validator
                ->date('data_inscricao')
                ->allowEmptyDate('data_inscricao');

        $validator
                ->date('data_selecao')
                ->allowEmptyDate('data_selecao');

        $validator
                ->scalar('horario_selecao')
                ->maxLength('horario_selecao', 8)
                ->allowEmptyString('horario_selecao');

        $validator
                ->scalar('local_selecao')
                ->maxLength('local_selecao', 70)
                ->allowEmptyString('local_selecao');

        $validator
                ->scalar('forma_selecao')
                ->maxLength('forma_selecao', 1)
                ->allowEmptyString('forma_selecao');

        $validator
                ->scalar('contato')
                ->maxLength('contato', 70)
                ->allowEmptyString('contato');

        $validator
                ->scalar('outras')
                ->allowEmptyString('outras');

        $validator
                ->scalar('periodo')
                ->maxLength('periodo', 6)
                ->allowEmptyString('periodo');

        $validator
                ->date('datafax')
                ->allowEmptyDate('datafax');

        $validator
                ->email('email')
                ->allowEmptyString('email');

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
        $rules->add($rules->existsIn(['instituicao_id'], 'Instituicoes'), ['errorField' => 'instituicao_id']);

        return $rules;
    }
}
