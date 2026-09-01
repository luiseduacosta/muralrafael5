<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstSuper Entity
 *
 * @property int $id
 * @property int $instituicao_id
 * @property int $supervisor_id
 *
 * @property \App\Model\Entity\Instituicao $instituicao
 * @property \App\Model\Entity\Supervisor $supervisor
 */
class InstSuper extends Entity
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
        'instituicao_id' => true,
        'supervisor_id' => true,
        'instituicao' => true,
        'supervisor' => true,
    ];
}
