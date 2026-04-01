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
            ->date('datanascimento')
            ->allowEmptyDate('datanascimento');

        $validator
            ->scalar('localnascimento')
            ->maxLength('localnascimento', 30)
            ->allowEmptyString('localnascimento');

        $validator
            ->nonNegativeInteger('ddd_telefone')
            ->notEmptyString('ddd_telefone');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 15)
            ->regex('telefone', '/^\([0-9]{2}\)\s[0-9]{4}\.[0-9]{4}$/', 'Insira um número de telefone válido')
            ->allowEmptyString('telefone');

        $validator
            ->nonNegativeInteger('ddd_celular')
            ->notEmptyString('ddd_celular');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 15)
            ->regex('celular', '/^\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}$/', 'Insira um número de celular válido')
            ->allowEmptyString('celular');

        $validator
            ->scalar('homepage')
            ->maxLength('homepage', 120)
            ->allowEmptyString('homepage');

        $validator
            ->scalar('redesocial')
            ->maxLength('redesocial', 50)
            ->allowEmptyString('redesocial');

        $validator
            ->scalar('curriculolattes')
            ->maxLength('curriculolattes', 50)
            ->allowEmptyString('curriculolattes');

        $validator
            ->date('atualizacaolattes')
            ->allowEmptyDate('atualizacaolattes');

        $validator
            ->scalar('curriculosigma')
            ->maxLength('curriculosigma', 7)
            ->allowEmptyString('curriculosigma'); // Obsoleto. Sistema da UFRJ destivado

        $validator
            ->scalar('pesquisadordgp')
            ->maxLength('pesquisadordgp', 20)
            ->allowEmptyString('pesquisadordgp'); // Diretorio de grupos de pesquisa do CNPq

        $validator
            ->scalar('formacaoprofissional')
            ->maxLength('formacaoprofissional', 30)
            ->allowEmptyString('formacaoprofissional');

        $validator
            ->scalar('universidadedegraduacao')
            ->maxLength('universidadedegraduacao', 50)
            ->allowEmptyString('universidadedegraduacao');

        $validator
            ->nonNegativeInteger('anoformacao')
            ->regex('anoformacao', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('anoformacao');

        $validator
            ->scalar('mestradoarea')
            ->maxLength('mestradoarea', 40)
            ->allowEmptyString('mestradoarea');

        $validator
            ->scalar('mestradouniversidade')
            ->maxLength('mestradouniversidade', 50)
            ->allowEmptyString('mestradouniversidade');

        $validator
            ->nonNegativeInteger('mestradoanoconclusao')
            ->regex('mestradoanoconclusao', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('mestradoanoconclusao');

        $validator
            ->scalar('doutoradoarea')
            ->maxLength('doutoradoarea', 40)
            ->allowEmptyString('doutoradoarea');

        $validator
            ->scalar('doutoradouniversidade')
            ->maxLength('doutoradouniversidade', 50)
            ->allowEmptyString('doutoradouniversidade');

        $validator
            ->nonNegativeInteger('doutoradoanoconclusao')
            ->regex('doutoradoanoconclusao', '/^(19|20)[0-9]{2}$/', 'Insira um ano válido')
            ->allowEmptyString('doutoradoanoconclusao');

        $validator
            ->date('dataingresso')
            ->allowEmptyDate('dataingresso');

        $validator
            ->scalar('formaingresso')
            ->maxLength('formaingresso', 100)
            ->allowEmptyString('formaingresso');

        $validator
            ->scalar('tipocargo')
            ->maxLength('tipocargo', 10)
            ->allowEmptyString('tipocargo'); // Tipo de cargo do professor: efetivo, subtituto, temporario, convidado

        $validator
            ->scalar('categoria')
            ->maxLength('categoria', 10)
            ->allowEmptyString('categoria'); // Categoria do professor: titular, associado, adjunto, auxiliar

        $validator
            ->scalar('regimetrabalho')
            ->maxLength('regimetrabalho', 5)
            ->allowEmptyString('regimetrabalho'); // Regime de trabalho do professor: 20, 40, 40DE

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
            ->scalar('sexo')
            ->allowEmptyString('sexo');

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
