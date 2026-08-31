<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Supervisores Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\InstituicoesTable&\Cake\ORM\Association\BelongsToMany $Instituicoes
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
        $this->belongsToMany('Instituicoes', [
            'foreignKey' => 'supervisor_id',
            'targetForeignKey' => 'instituicao_id',
            'joinTable' => 'inst_super',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 70)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 15)
            ->allowEmptyString('cpf');

        $validator
            ->nonNegativeInteger('codigo_telefone')
            ->allowEmptyString('codigo_telefone');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 15)
            ->regex('telefone', '/^\([0-9]{2}\) [0-9]{4,5}\.[0-9]{4}$/', 'Telefone inválido')
            ->allowEmptyString('telefone');

        $validator
            ->nonNegativeInteger('codigo_celular')
            ->allowEmptyString('codigo_celular');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 15)
            ->regex('celular', '/^\([0-9]{2}\) [0-9]{4,5}\.[0-9]{4}$/', 'Celular inválido')
            ->allowEmptyString('celular');

        $validator
            ->email('email')
            ->maxLength('email', 255)
            ->allowEmptyString('email');

        $validator
            ->scalar('escola')
            ->maxLength('escola', 70)
            ->allowEmptyString('escola');

        $validator
            ->scalar('ano_formacao')
            ->maxLength('ano_formacao', 4)
            ->regex('ano_formacao', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('ano_formacao');

        $validator
            ->scalar('cress')
            ->maxLength('cress', 10)
            ->allowEmptyString('cress');

        $validator
            ->nonNegativeInteger('regiao')
            ->notEmptyString('regiao', null, 'create');

        $validator
            ->scalar('cargo')
            ->maxLength('cargo', 25)
            ->allowEmptyString('cargo');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->integer('estagiarios_count')
            ->allowEmptyString('estagiarios_count');

        return $validator;
    }
}
