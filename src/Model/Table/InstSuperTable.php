<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * InstSuper Model
 *
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsTo $Instituicoes
 * @property \App\Model\Table\SupervisoresTable&\Cake\ORM\Association\BelongsTo $Supervisores
 * @method \App\Model\Entity\InstSuper newEmptyEntity()
 * @method \App\Model\Entity\InstSuper newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\InstSuper[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\InstSuper get($primaryKey, $options = [])
 * @method \App\Model\Entity\InstSuper findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\InstSuper patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\InstSuper[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\InstSuper|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InstSuper saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InstSuper[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\InstSuper[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\InstSuper[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\InstSuper[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class InstSuperTable extends Table
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

        $this->setTable('inst_super');
        $this->setAlias('InstSuper');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Instituicoes', [
            'foreignKey' => 'instituicao_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Supervisores', [
            'foreignKey' => 'supervisor_id',
            'joinType' => 'INNER',
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
            ->integer('instituicao_id')
            ->notEmptyString('instituicao_id');

        $validator
            ->integer('supervisor_id')
            ->notEmptyString('supervisor_id');

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
        $rules->add($rules->existsIn(['supervisor_id'], 'Supervisores'), ['errorField' => 'supervisor_id']);

        return $rules;
    }
}
