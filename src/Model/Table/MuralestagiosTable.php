<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Muralestagios Model
 *
 * @property \App\Model\Table\InscricoesTable&\Cake\ORM\Association\HasMany $Inscricoes
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsTo $Instituicoes
 * @property \App\Model\Table\ProfessoresTable&\Cake\ORM\Association\BelongsTo $Professores
 * @property \App\Model\Table\TurmasTable&\Cake\ORM\Association\BelongsTo $Turmas
 * @property \App\Model\Table\TurnosTable&\Cake\ORM\Association\BelongsTo $Turnos
 *
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
 
class MuralestagiosTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('mural_estagios');
        $this->setAlias('Muralestagios');
        $this->setDisplayField('instituicao_id');
        $this->setPrimaryKey('id');
            
        $this->hasMany('Inscricoes', [
            'foreignKey' => ['mural_estagio_id'],
        ]);
            
        $this->belongsTo('Instituicoes', [
            'foreignKey' => ['instituicao_id'],
        ]);
        $this->belongsTo('Turmas', [
            'foreignKey' => ['turma_id']
        ]);
        $this->belongsTo('Turnos', [
            'foreignKey' => ['turno_id']
        ]);
        $this->belongsTo('Professores', [
            'foreignKey' => ['professor_id'],
        ]);
    }

    public function beforeFind($event, $query, $options, $primary) {

        $query->order(['Muralestagios.id' => 'ASC']);
        return $query;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator {
        $validator
                ->integer('id')
                ->allowEmptyString('id', null, 'create');

        $validator
                ->scalar('instituicao_id')
                ->maxLength('instituicao_id', 100)
                ->notEmptyString('instituicao_id');

        $validator
                ->scalar('convenio')
                ->maxLength('convenio', 1)
                ->notEmptyString('convenio');

        $validator
                ->notEmptyString('vagas');

        $validator
                ->scalar('beneficios')
                ->maxLength('beneficios', 50)
                ->allowEmptyString('beneficios');

        $validator
                ->scalar('fim_de_semana')
                ->maxLength('fim_de_semana', 1)
                ->allowEmptyString('fim_de_semana');

        $validator
                ->allowEmptyString('carga_horaria');

        $validator
                ->scalar('requisitos')
                ->maxLength('requisitos', 255)
                ->allowEmptyString('requisitos');

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
                ->maxLength('horario_selecao', 5)
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
    public function buildRules(RulesChecker $rules): RulesChecker {
        $rules->add($rules->existsIn(['instituicao_id'], 'Instituicoes'), ['errorField' => 'instituicao_id']);
        $rules->add($rules->existsIn(['professor_id'], 'Professores'), ['errorField' => 'professor_id']);
        $rules->add($rules->existsIn(['turma_id'], 'Turmas'), ['errorField' => 'turma_id']);
        $rules->add($rules->existsIn(['turno_id'], 'Turnos'), ['errorField' => 'turno_id']);

        return $rules;
    }

}
