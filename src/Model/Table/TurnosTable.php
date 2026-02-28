<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Turnos Model
 *
 * @property \App\Model\Table\EstagiariosTable&\Cake\ORM\Association\HasMany $Estagiarios
 *
 * @method \App\Model\Entity\Turno newEmptyEntity()
 * @method \App\Model\Entity\Turno newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Turno> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Turno get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Turno findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Turno patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Turno> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Turno|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Turno saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Turno>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Turno>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Turno>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Turno> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Turno>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Turno>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Turno>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Turno> deleteManyOrFail(iterable $entities, array $options = [])
 */
class TurnosTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('turnos');
        $this->setAlias('Turnos');
        $this->setDisplayField('turno');
        $this->setPrimaryKey('id');

        $this->hasMany('Estagiarios', [
            'foreignKey' => 'turno_id',
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
            ->scalar('turno')
            ->requirePresence('turno', 'create')
            ->notEmptyString('turno');

        return $validator;
    }
}
