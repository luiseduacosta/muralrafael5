<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Estagiarios Model
 *
 * @property \App\Model\Table\AlunosTable&\Cake\ORM\Association\BelongsTo $Alunos
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsTo $Instituicoes
 * @property \App\Model\Table\SupervisoresTable&\Cake\ORM\Association\BelongsTo $Supervisores
 * @property \App\Model\Table\ProfessoresTable&\Cake\ORM\Association\BelongsTo $Professores
 * @property \App\Model\Table\FolhadeatividadesTable&\Cake\ORM\Association\HasMany $Folhadeatividades
 * @property \App\Model\Table\AvaliacoesTable&\Cake\ORM\Association\HasOne $Avaliacoes
 * @method \App\Model\Entity\Estagiario newEmptyEntity()
 * @method \App\Model\Entity\Estagiario newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Estagiario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Estagiario get($primaryKey, $options = [])
 * @method \App\Model\Entity\Estagiario findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Estagiario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Estagiario[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Estagiario|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Estagiario saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EstagiariosTable extends Table
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

        $this->setTable('estagiarios');
        $this->setAlias('Estagiarios');
        $this->setDisplayField('registro');
        $this->setPrimaryKey('id');

        $this->belongsTo('Alunos', [
            'foreignKey' => 'aluno_id',
        ]);
        $this->belongsTo('Instituicoes', [
            'foreignKey' => 'instituicao_id',
        ]);
        $this->belongsTo('Supervisores', [
            'foreignKey' => 'supervisor_id',
        ]);
        $this->belongsTo('Professores', [
            'foreignKey' => 'professor_id',
        ]);
        $this->hasMany('Folhadeatividades', [
            'foreignKey' => 'estagiario_id',
        ]);
        $this->hasOne('Avaliacoes', [
            'foreignKey' => 'estagiario_id',
        ]);

        $this->addBehavior('CounterCache', [
            'Supervisores' => ['estagiarios_count'],
            'Professores' => ['estagiarios_count'],
            'Instituicoes' => ['estagiarios_count'],
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
            ->scalar('ajuste2020')
            ->maxLength('ajuste2020', 1)
            ->inList('ajuste2020', ['0', '1'])
            ->notEmptyString('ajuste2020');

        $validator
            ->scalar('nivel')
            ->maxLength('nivel', 1)
            ->inList('nivel', ['1', '2', '3', '4', '9'])
            ->notEmptyString('nivel');

        $validator
            ->allowEmptyString('tc')
            ->inList('tc', ['0', '1']);

        $validator
            ->date('tc_solicitacao')
            ->allowEmptyDate('tc_solicitacao');

        $validator
            ->scalar('periodo')
            ->maxLength('periodo', 6)
            ->notEmptyString('periodo');

        $validator
            ->decimal('nota')
            ->allowEmptyString('nota');

        $validator
            ->nonNegativeInteger('ch')
            ->allowEmptyString('ch');

        $validator
            ->scalar('observacoes')
            ->maxLength('observacoes', 255)
            ->allowEmptyString('observacoes');

        $validator
            ->boolean('benetransporte')
            ->allowEmptyString('benetransporte');

        $validator
            ->boolean('benealimentacao')
            ->allowEmptyString('benealimentacao');

        $validator
            ->scalar('benebolsa')
            ->maxLength('benebolsa', 5)
            ->allowEmptyString('benebolsa');

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
        $rules->add($rules->existsIn(['instituicao_id'], 'Instituicoes'), ['errorField' => 'instituicao_id']);
        $rules->add($rules->existsIn(['supervisor_id'], 'Supervisores'), ['errorField' => 'supervisor_id']);
        $rules->add($rules->existsIn(['professor_id'], 'Professores'), ['errorField' => 'professor_id']);

        return $rules;
    }
}
