<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Estagiario Entity
 *
 * @property int $id
 * @property int $aluno_id
 * @property int $registro
 * @property string $ajuste2020
 * @property string $nivel
 * @property int $tc
 * @property \Cake\I18n\FrozenDate|null $tc_solicitacao
 * @property int $instituicao_id
 * @property int|null $supervisor_id
 * @property int|null $professor_id
 * @property string $periodo
 * @property int $turmaestagio_id
 * @property int $turno_id
 * @property string|null $nota
 * @property int|null $ch
 * @property string|null $observacoes
 * @property int|null $alunoestagiario_id
 * @property string|null $turno
 * @property bool|null $benetransporte
 * @property bool|null $benealimentacao
 * @property string|null $benebolsa
 * @property int|null $complemento_id
 *
 * @property \App\Model\Entity\Aluno $aluno
 * @property \App\Model\Entity\Instituicao $instituicao
 * @property \App\Model\Entity\Supervisor $supervisor
 * @property \App\Model\Entity\Professor $professor
 * @property \App\Model\Entity\Turma $turma
 * @property \App\Model\Entity\Turno $turno_rel
 * @property \App\Model\Entity\Complemento $complemento
 */
class Estagiario extends Entity
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
        'aluno_id' => true,
        'registro' => true,
        'ajuste2020' => true,
        'nivel' => true,
        'tc' => true,
        'tc_solicitacao' => true,
        'instituicao_id' => true,
        'supervisor_id' => true,
        'professor_id' => true,
        'periodo' => true,
        'turmaestagio_id' => true,
        'turno_id' => true,
        'nota' => true,
        'ch' => true,
        'observacoes' => true,
        'alunoestagiario_id' => true,
        'turno' => true,
        'benetransporte' => true,
        'benealimentacao' => true,
        'benebolsa' => true,
        'complemento_id' => true,
        'turno_entidade' => true,
        'aluno' => true,
        'instituicao' => true,
        'supervisor' => true,
        'professor' => true,
        'turma' => true,
        'turno_rel' => true,
        'complemento' => true,
    ];
}
