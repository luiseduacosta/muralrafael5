<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Configuracoes Model
 *
 * @method \App\Model\Entity\Configuracao newEmptyEntity()
 * @method \App\Model\Entity\Configuracao newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Configuracao[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Configuracao get($primaryKey, $options = [])
 * @method \App\Model\Entity\Configuracao findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Configuracao patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Configuracao[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Configuracao|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Configuracao saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Configuracao[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Configuracao[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Configuracao[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Configuracao[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ConfiguracoesTable extends Table
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

        $this->setTable('configuracoes');
        $this->setAlias('Configuracoes');
        $this->setDisplayField('instituicao');
        $this->setPrimaryKey('id');

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
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }
}
