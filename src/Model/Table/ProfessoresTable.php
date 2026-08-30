<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Professores Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @method \App\Model\Entity\Professor newEmptyEntity()
 * @method \App\Model\Entity\Professor newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Professor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Professor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Professor findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Professor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Professor[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Professor|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Professor saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProfessoresTable extends Table
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

        $this->setTable('professores');
        $this->setAlias('Professores');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Estagiarios', [
            'foreignKey' => 'professor_id',
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
            ->maxLength('nome', 50)
            ->notEmptyString('nome');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 14)
            ->allowEmptyString('cpf');

        $validator
            ->scalar('siape') // SIAPE: 8 dígitos. Pode começar com 0. Não pode ter pontos ou hífens
            ->maxLength('siape', 8)
            ->allowEmptyString('siape');

        $validator
            ->scalar('codigo_telefone')
            ->maxLength('codigo_telefone', 2)
            ->allowEmptyString('codigo_telefone');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 15)
            ->regex('telefone', '/^\([0-9]{2}\)\s[0-9]{4}\.[0-9]{4}$/', 'Insira um número de telefone válido')
            ->allowEmptyString('telefone');

        $validator
            ->scalar('codigo_celular')
            ->maxLength('codigo_celular', 2)
            ->allowEmptyString('codigo_celular');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 15)
            ->regex('celular', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Insira um número de celular válido')
            ->allowEmptyString('celular');

        $validator
            ->scalar('curriculolattes')
            ->maxLength('curriculolattes', 50)
            ->allowEmptyString('curriculolattes');

        $validator
            ->date('atualizacaolattes')
            ->allowEmptyDate('atualizacaolattes');

        $validator
            ->date('dataingresso')
            ->allowEmptyDate('dataingresso');

        $validator
            ->scalar('departamento')
            ->maxLength('departamento', 30)
            ->allowEmptyString('departamento');

        $validator
            ->date('dataegresso')
            ->allowEmptyDate('dataegresso');

        $validator
            ->scalar('motivoegresso')
            ->maxLength('motivoegresso', 100)
            ->allowEmptyString('motivoegresso');

        $validator
            ->nonNegativeInteger('cress')
            ->allowEmptyString('cress');

        $validator
            ->nonNegativeInteger('regiao')
            ->allowEmptyString('regiao');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->scalar('estagiarios_count')
            ->allowEmptyString('estagiarios_count');

        return $validator;
    }
}
