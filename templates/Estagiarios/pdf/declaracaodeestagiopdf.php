<?php
/**
 * Declaração de Estágio PDF
 * 
 * @var \App\Model\Entity\Estagiario $estagiario
 */
namespace App\View\PDF; 
use Cake\I18n\I18n;
use Cake\I18n\Date;

I18n::setLocale('pt-BR');
$hoje = Date::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->layout = 'default';
$this->assign('title', 'Declaração de Estágio');

?>
<?php

$nivel = $estagiario->nivel;
if ($nivel == 9) {
    $nivel = "estágio extra-curricular";
}

$horas = $estagiario->ch;
if (empty($horas) && $horas === '0') {
    $horas = '_____';
}
// die();
$supervisora = $estagiario->supervisor->nome;
if (empty($supervisora))
    $supervisora = "______________________________________";

$regiao = $estagiario->supervisor->regiao;
if (empty($regiao)) {
    $regiao = '___';
}

$cress = $estagiario->supervisor->cress;
if (empty($cress)) {
    $cress = '_______';
}

?>

<h1 style="text-align:center">
Coordenação de Estágio<br />
Declaração de Estágio Curricular
</h1>
<br />
<br />
<p style="text-align:justify; line-height: 2.5;">
Declaramos que o/a aluno <b><?= $estagiario->aluno->nome; ?></b> 
inscrito(a) no CPF sob o nº <?= $estagiario->aluno->cpf; ?> 
e no RG nº <?= $estagiario->aluno->identidade; ?> 
expedido por <?= $estagiario->aluno->orgao; ?>, 
matriculado(a) no Curso de Serviço Social da 
Universidade Federal do Rio de Janeiro com o número <?= $estagiario->aluno->registro; ?>, 
estagiou na instituição <b><?= $estagiario->instituicao->instituicao; ?></b>, 
com a supervisão profissional do/a Assistente Social <b><?= $supervisora ?? '____________________'; ?></b> 
registrada no CRESS <?= $regiao ?? '____'; ?>&ordf; região 
com o número <?= $cress ?? '____'; ?>, 
no semestre de <?= $estagiario->periodo ?? '____'; ?>, 
com uma carga horária de <?= $horas ?? '____'; ?> horas.
<p>

<p style="text-align:justify; line-height: 2.5;">
As atividades desenvolvidas correspondem ao 
nível <?= $estagiario->nivel; ?> do currículo da Escola de Serviço Social da UFRJ.
</p>
<br />
<br />
<p style="text-align:right">Rio de Janeiro, <?= $dia; ?> de <?= $mes; ?> de <?= $ano; ?>.</p>

<br style='line-height: 10.0'/>

<table style="width:100%">
<tr>
<td style="text-decoration: overline;">Coordenação de Estágio</td>
<td style="text-decoration: overline;"><?= $estagiario->aluno->nome ?></td>
<td style="text-decoration: overline;"><?= $supervisora ?></td>
</tr>

<tr>
<td>Escola de Serviço Social</td>
<td>DRE: <?= $estagiario->aluno->registro; ?></td>
<td>CRESS: <?= $cress; ?> da <?= $regiao; ?>&ordf; Região</td>
</tr>

<tr>
<td>UFRJ</td>
<td></td>
<td></td>
</tr>

</table>
