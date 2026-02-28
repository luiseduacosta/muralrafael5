<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Aluno Entity
 *
 * @property int $id
 * @property string $nome
 * @property int $registro
 * @property int $codigo_telefone
 * @property string|null $telefone
 * @property int $codigo_celular
 * @property string|null $celular
 * @property string|null $cpf
 * @property string|null $identidade
 * @property string|null $orgao
 * @property \Cake\I18n\FrozenDate|null $nascimento
 * @property string|null $endereco
 * @property string|null $cep
 * @property string|null $municipio
 * @property string|null $bairro
 * @property string|null $observacoes
 * @property int $user_id
 *
 * @property \App\Model\Entity\User[] $user
 */
class Aluno extends Entity
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
        'registro' => true,
        'codigo_telefone' => true,
        'telefone' => true,
        'codigo_celular' => true,
        'celular' => true,
        'cpf' => true,
        'identidade' => true,
        'orgao' => true,
        'nascimento' => true,
        'endereco' => true,
        'cep' => true,
        'municipio' => true,
        'bairro' => true,
        'observacoes' => true,
        'user_id' => true
 
    ];
}
