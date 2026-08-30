<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Professor Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $cpf
 * @property int|null $siape
 * @property string $codigo_telefone
 * @property string|null $telefone
 * @property string $codigo_celular
 * @property string|null $celular
 * @property string|null $curriculolattes
 * @property \Cake\I18n\FrozenDate|null $atualizacaolattes
 * @property \Cake\I18n\FrozenDate|null $dataingresso
 * @property string|null $departamento
 * @property \Cake\I18n\FrozenDate|null $dataegresso
 * @property string|null $motivoegresso
 * @property string|null $observacoes
 * @property int|null $cress
 * @property int|null $regiao
 * @property string|null $email
 * @property int $user_id
 * @property int|null $estagiarios_count
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Estagiario[] $estagiarios
 */
class Professor extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'nome' => true,
        'cpf' => true,
        'siape' => true,
        'codigo_telefone' => true,
        'telefone' => true,
        'codigo_celular' => true,
        'celular' => true,
        'curriculolattes' => true,
        'atualizacaolattes' => true,
        'dataingresso' => true,
        'departamento' => true,
        'dataegresso' => true,
        'motivoegresso' => true,
        'observacoes' => true,
        'cress' => true,
        'regiao' => true,
        'email' => true,
        'user_id' => true,
        'user' => true,
        'estagiarios' => true,
        'estagiarios_count' => true,
    ];
}
