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
 * @property string|null $endereco
 * @property string|null $bairro
 * @property string|null $municipio
 * @property string|null $cep
 * @property string|null $codigo_tel
 * @property string|null $telefone
 * @property string|null $codigo_cel
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $escola
 * @property string|null $ano_formatura
 * @property int $cress
 * @property int $regiao
 * @property string|null $outros_estudos
 * @property string|null $area_curso
 * @property string|null $ano_curso
 * @property string|null $cargo
 * @property int|null $num_inscricao
 * @property string|null $curso_turma
 * @property string|null $observacoes
 * @property int $user_id
 * @property int|null $estagiarios_count
 *
 * @property \App\Model\Entity\Estagiario[] $estagiarios
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Instituicao[] $instituicao
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
     * @var array
     */
    protected array $_accessible = [
        'nome' => true,
        'cpf' => true,
        'endereco' => true,
        'bairro' => true,
        'municipio' => true,
        'cep' => true,
        'codigo_tel' => true,
        'telefone' => true,
        'codigo_cel' => true,
        'celular' => true,
        'email' => true,
        'escola' => true,
        'ano_formatura' => true,
        'cress' => true,
        'regiao' => true,
        'outros_estudos' => true,
        'area_curso' => true,
        'ano_curso' => true,
        'cargo' => true,
        'num_inscricao' => true,
        'curso_turma' => true,
        'observacoes' => true,
        'user_id' => true,
        'estagiarios_count' => true,
        'estagiarios' => true,
        'users' => true,
        'instituicao' => true,
    ];
}
