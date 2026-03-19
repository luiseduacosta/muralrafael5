<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Muralestagio Entity
 *
 * @property int $id
 * @property int|null $instituicao_id
 * @property string $instituicao
 * @property string $convenio
 * @property int $vagas
 * @property string|null $beneficios
 * @property string|null $final_de_semana
 * @property int|null $carga_horaria
 * @property string|null $requisitos
 * @property string|null $horario
 * @property \Cake\I18n\FrozenDate|null $data_selecao
 * @property \Cake\I18n\FrozenDate|null $data_inscricao
 * @property string|null $horario_selecao
 * @property string|null $local_selecao
 * @property string|null $forma_selecao
 * @property string|null $contato
 * @property string|null $outras
 * @property string|null $periodo
 * @property \Cake\I18n\FrozenDate|null $datafax
 * @property string $local_inscricao
 * @property string|null $email
 *
 * @property \App\Model\Entity\Instituicao $instituicao_rel
 */

class Muralestagio extends Entity
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
        'instituicao_id' => true,
        'instituicao' => true,
        'convenio' => true,
        'vagas' => true,
        'beneficios' => true,
        'final_de_semana' => true,
        'carga_horaria' => true,
        'requisitos' => true,
        'horario' => true,
        'data_selecao' => true,
        'data_inscricao' => true,
        'horario_selecao' => true,
        'local_selecao' => true,
        'forma_selecao' => true,
        'contato' => true,
        'outras' => true,
        'periodo' => true,
        'datafax' => true,
        'local_inscricao' => true,
        'email' => true,
        'instituicao_rel' => true,
    ];
}
