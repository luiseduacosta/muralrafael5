<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Complementos Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Complemento newEmptyEntity()
 * @method \App\Model\Entity\Complemento newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Complemento[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Complemento get($primaryKey, $options = [])
 * @method \App\Model\Entity\Complemento findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Complemento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Complemento[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Complemento|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Complemento saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ComplementosTable extends Table
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

        $this->setTable('complementos');
        $this->setAlias('Complementos');
        $this->setDisplayField('periodo_especial');
        $this->setPrimaryKey('id');

        $this->belongsTo('Estagiarios', [
            'foreignKey' => 'complemento_id',
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
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }
}
