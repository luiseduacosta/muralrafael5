<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Folhadeatividade Entity
 *
 * @property int $id
 * @property int $estagiario_id
 * @property \Cake\I18n\Date $dia
 * @property \Cake\I18n\Time $inicio
 * @property \Cake\I18n\Time $final
 * @property \Cake\I18n\Time|null $horario
 * @property string $atividade
 *
 * @property \App\Model\Entity\Estagiario $estagiario
 */
class Folhadeatividade extends Entity
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
        'estagiario_id' => true,
        'dia' => true,
        'inicio' => true,
        'final' => true,
        'horario' => true,
        'atividade' => true,
        'estagiario' => true,
    ];
}
