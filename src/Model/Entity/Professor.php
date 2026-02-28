<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Professor Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $cpf
 * @property int|null $siape
 * @property \Cake\I18n\FrozenDate|null $datanascimento
 * @property string|null $localnascimento
 * @property string $ddd_telefone
 * @property string|null $telefone
 * @property string $ddd_celular
 * @property string|null $celular
 * @property string|null $homepage
 * @property string|null $redesocial
 * @property string|null $curriculolattes
 * @property \Cake\I18n\FrozenDate|null $atualizacaolattes
 * @property string|null $curriculosigma
 * @property string|null $pesquisadordgp
 * @property string|null $formacaoprofissional
 * @property string|null $universidadedegraduacao
 * @property int|null $anoformacao
 * @property string|null $mestradoarea
 * @property string|null $mestradouniversidade
 * @property int|null $mestradoanoconclusao
 * @property string|null $doutoradoarea
 * @property string|null $doutoradouniversidade
 * @property int|null $doutoradoanoconclusao
 * @property \Cake\I18n\FrozenDate|null $dataingresso
 * @property string|null $formaingresso
 * @property string|null $tipocargo
 * @property string|null $regimetrabalho
 * @property string|null $departamento
 * @property \Cake\I18n\FrozenDate|null $dataegresso
 * @property string|null $motivoegresso
 * @property string|null $observacoes
 * @property int $user_id
 *
 * @property \App\Model\Entity\User[] $users
 */
class Professor extends Entity
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
        'siape' => true,
        'datanascimento' => true,
        'localnascimento' => true,
        'ddd_telefone' => true,
        'telefone' => true,
        'ddd_celular' => true,
        'celular' => true,
        'homepage' => true,
        'redesocial' => true,
        'curriculolattes' => true,
        'atualizacaolattes' => true,
        'curriculosigma' => true,
        'pesquisadordgp' => true,
        'formacaoprofissional' => true,
        'universidadedegraduacao' => true,
        'anoformacao' => true,
        'mestradoarea' => true,
        'mestradouniversidade' => true,
        'mestradoanoconclusao' => true,
        'doutoradoarea' => true,
        'doutoradouniversidade' => true,
        'doutoradoanoconclusao' => true,
        'dataingresso' => true,
        'formaingresso' => true,
        'tipocargo' => true,
        'regimetrabalho' => true,
        'departamento' => true,
        'dataegresso' => true,
        'motivoegresso' => true,
        'observacoes' => true,
        'estagiarios' => true,
        'muralestagios' => true,
        'user_id' => true
    ];
}
