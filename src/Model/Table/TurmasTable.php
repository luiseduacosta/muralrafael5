<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Turmas Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\MuralestagiosTable&\Cake\ORM\Association\HasMany $Muralestagios
 *
 * @method \App\Model\Entity\Turma newEmptyEntity()
 * @method \App\Model\Entity\Turma newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Turma[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Turma get($primaryKey, $options = [])
 * @method \App\Model\Entity\Turma findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Turma patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Turma[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Turma|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Turma saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Turma[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Turma[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Turma[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Turma[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TurmasTable extends Table
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

        $this->setTable('turmas');
        $this->setAlias('Turmas');
        $this->setDisplayField('turma');
        $this->setPrimaryKey('id');
        
        $this->hasMany('Estagiarios', [
            'foreignKey' => 'turma_id',
        ]);
        $this->hasMany('Muralestagios', [
            'foreignKey' => 'turma_id',
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

        $validator
            ->scalar('turma')
            ->maxLength('turma', 70)
            ->notEmptyString('turma');

        return $validator;
    }
}
