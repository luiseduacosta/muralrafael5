<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Inscricoes Model
 *
 * @property \App\Model\Table\AlunosTable&\Cake\ORM\Association\BelongsTo $Alunos
 * @property \App\Model\Table\MuralestagiosTable&\Cake\ORM\Association\BelongsTo $Muralestagios
 * @method \App\Model\Entity\Inscricao newEmptyEntity()
 * @method \App\Model\Entity\Inscricao newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Inscricao[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Inscricao get($primaryKey, $options = [])
 * @method \App\Model\Entity\Inscricao findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Inscricao patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Inscricao[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Inscricao|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Inscricao saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class InscricoesTable extends Table
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

        $this->setTable('inscricoes');
        $this->setAlias('Inscricoes');
        $this->setDisplayField('registro');
        $this->setPrimaryKey('id');

        $this->belongsTo('Alunos', [
            'foreignKey' => 'aluno_id',
        ]);
        $this->belongsTo('Muralestagios', [
            'foreignKey' => 'muralestagio_id',
        ]);

        $this->addBehavior('CounterCache', [
            'Alunos' => ['inscricao_count'],
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
                ->integer('registro')
                ->notEmptyString('registro');
        $validator
                ->integer('aluno_id')
                ->notEmptyString('aluno_id');

        $validator
                ->integer('muralestagio_id')
                ->notEmptyString('muralestagio_id');
        $validator
                ->date('data')
                ->requirePresence('data', 'create')
                ->notEmptyDate('data');

        $validator
                ->scalar('periodo')
                ->maxLength('periodo', 6)
                ->notEmptyString('periodo');

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
        $rules->add($rules->existsIn(['aluno_id'], 'Alunos'), ['errorField' => 'aluno_id']);
        $rules->add($rules->existsIn(['muralestagio_id'], 'Muralestagios'), ['errorField' => 'muralestagio_id']);

        return $rules;
    }
}
