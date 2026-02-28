<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Instituicao Entity
 *
 * @property int $id
 * @property string $instituicao
 * @property int|null $area_id
 * @property string|null $natureza
 * @property string $cnpj
 * @property string $email
 * @property string|null $url
 * @property string $endereco
 * @property string $bairro
 * @property string $municipio
 * @property string $cep
 * @property string $telefone
 * @property string|null $beneficio
 * @property string|null $fim_de_semana
 * @property string $local_inscricao
 * @property int $convenio
 * @property \Cake\I18n\FrozenDate|null $expira
 * @property string $seguro
 * @property string $avaliacao
 * @property string|null $observacoes
 *
 * @property \App\Model\Entity\Area[] $area
 */
class Instituicao extends Entity
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
        'instituicao' => true,
        'area_id' => true,
        'natureza' => true,
        'cnpj' => true,
        'email' => true,
        'url' => true,
        'endereco' => true,
        'bairro' => true,
        'municipio' => true,
        'cep' => true,
        'telefone' => true,
        'beneficio' => true,
        'fim_de_semana' => true,
        'local_inscricao' => true,
        'convenio' => true,
        'expira' => true,
        'seguro' => true,
        'avaliacao' => true,
        'observacoes' => true
    ];
}
