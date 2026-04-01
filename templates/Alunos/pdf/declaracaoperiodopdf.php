<?php
/**
 * Certificado de Período PDF
 * 
 * @var \App\Model\Entity\Aluno $aluno
 * @var int $totalperiodos
 */
namespace App\View\PDF; 
use Cake\I18n\I18n;
use Cake\I18n\Date;

I18n::setLocale('pt-BR');
$hoje = Date::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

if ($aluno->turno && $aluno->turno->turno == 'diurno') {
    $duracaocurso = '8';
} elseif ($aluno->turno && $aluno->turno->turno == 'noturno') {
    $duracaocurso = '10';
}

$this->layout = 'default';
$this->assign('title', 'Certificado de Período');
?>

<h1 style="text-align:center">
    <!-- Logo rendered by PDF layout -->
    Coordenação de Estágio<br />
    Declaração
</h1>
<br />
<br />
<p style="text-align:justify; line-height: 2.5;">
    Declaramos que o/a aluno/a <b><?= h($aluno->nome) ?></b> 
    inscrito(a) no CPF sob o nº <?= h($aluno->cpf) ?> 
    e no RG nº <?= h($aluno->identidade) ?> 
    expedido por <?= h($aluno->orgao) ?>, 
    matriculado(a) no Curso de Serviço Social da 
    Universidade Federal do Rio de Janeiro com o número <?= h($aluno->registro) ?>, 
    ingressou em <?= h($aluno->ingresso) ?> no turno <?= ucfirst(h($aluno->turno->turno ?? '')) ?>
    cursando atualmente <?= $totalperiodos ?><sup>o</sup> período.
</p>

<p style="text-align:justify; line-height: 2.5;">
    O turno <?= ucfirst(h($aluno->turno->turno ?? '')) ?> do curso de Serviço Social consta de <?= ($aluno->turno && $aluno->turno->turno == 'diurno') ? '8' : '10' ?> semestres.
</p>
<br />
<br />
<p style="text-align:right">Rio de Janeiro, <?= $hoje->i18nFormat("dd ' de ' MMMM ' de ' yyyy") ?>.</p>

<br style='line-height: 10.0'/>


<table style="margin-left: auto; margin-right: auto;">
        <tr style="text-align:center">
            <td style="text-decoration: overline;">Coordenação de Estágio</td>
        </tr>
        <tr style="text-align:center">
            <td>Escola de Serviço Social</td>
        </tr>
        <tr style="text-align:center">
            <td>Universidade Federal do Rio de Janeiro</td>
        </tr>
</table>
