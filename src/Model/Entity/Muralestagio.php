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
 * @property int|null $cargaHoraria
 * @property string|null $requisitos
 * @property string|null $horario
 * @property int|null $professor_id
 * @property \Cake\I18n\FrozenDate|null $dataSelecao
 * @property \Cake\I18n\FrozenDate|null $dataInscricao
 * @property string|null $horarioSelecao
 * @property string|null $localSelecao
 * @property string|null $formaSelecao
 * @property string|null $contato
 * @property string|null $outras
 * @property string|null $periodo
 * @property \Cake\I18n\FrozenDate|null $datafax
 * @property string $localInscricao
 * @property string|null $email
 *
 * @property \App\Model\Entity\Instituicao $instituicao_rel
 * @property \App\Model\Entity\Professor $professor
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
        'cargaHoraria' => true,
        'requisitos' => true,
        'horario' => true,
        'professor_id' => true,
        'dataSelecao' => true,
        'dataInscricao' => true,
        'horarioSelecao' => true,
        'localSelecao' => true,
        'formaSelecao' => true,
        'contato' => true,
        'outras' => true,
        'periodo' => true,
        'datafax' => true,
        'localInscricao' => true,
        'email' => true,
        'instituicao_rel' => true,
        'professor' => true,
    ];
}
