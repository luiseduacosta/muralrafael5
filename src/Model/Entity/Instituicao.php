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
 * @property int|null $area
 * @property string|null $natureza
 * @property string $cnpj
 * @property string|null $email
 * @property string|null $url
 * @property string|null $endereco
 * @property string|null $bairro
 * @property string|null $municipio
 * @property string|null $cep
 * @property string|null $telefone
 * @property string|null $beneficios
 * @property string|null $fim_de_semana
 * @property int|null $convenio
 * @property \Cake\I18n\Date|null $expira
 * @property string|null $seguro
 * @property string|null $observacoes
 * @property int $user_id
 * @property int|null $estagiarios_count
 *
 * @property \App\Model\Entity\Area $Area
 * @property \App\Model\Entity\Estagiario[] $estagiarios
 * @property \App\Model\Entity\Muralestagio[] $muralestagios
 * @property \App\Model\Entity\Visita[] $visitas
 * @property \App\Model\Entity\Supervisor[] $supervisores
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
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'instituicao' => true,
        'area_id' => true,
        'area' => true,
        'natureza' => true,
        'cnpj' => true,
        'email' => true,
        'url' => true,
        'endereco' => true,
        'bairro' => true,
        'municipio' => true,
        'cep' => true,
        'telefone' => true,
        'beneficios' => true,
        'fim_de_semana' => true,
        'convenio' => true,
        'expira' => true,
        'seguro' => true,
        'observacoes' => true,
        'user_id' => true,
        'estagiarios' => true,
        'muralestagios' => true,
        'visitas' => true,
        'supervisores' => true,
        'estagiarios_count' => true,
    ];
}
