<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Supervisor Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $cpf
 * @property int|null $codigo_telefone
 * @property string|null $telefone
 * @property int|null $codigo_celular
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $escola
 * @property string|null $ano_formacao
 * @property string|null $cress
 * @property int $regiao
 * @property string|null $cargo
 * @property string|null $observacoes
 * @property int|null $user_id
 * @property int|null $estagiarios_count
 *
 * @property \App\Model\Entity\Estagiario[] $estagiarios
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Instituicao[] $instituicoes
 */
class Supervisor extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'nome' => true,
        'cpf' => true,
        'codigo_telefone' => true,
        'telefone' => true,
        'codigo_celular' => true,
        'celular' => true,
        'email' => true,
        'escola' => true,
        'ano_formacao' => true,
        'cress' => true,
        'regiao' => true,
        'cargo' => true,
        'observacoes' => true,
        'user_id' => true,
        'estagiarios_count' => true,
        'estagiarios' => true,
        'user' => true,
        'instituicoes' => true,
    ];
}
