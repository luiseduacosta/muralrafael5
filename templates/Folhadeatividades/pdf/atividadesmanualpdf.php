<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade $folhadeatividade
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
$this->assign('title', 'Atividades');
?>

<style>

table {
    width: 100%;
}

table tr td {
  border: 1px solid black;
  border-collapse: collapse;
  font-size: 80%;
  height: 20px; 
}

td {
    text-align: center !important;
}

.auxiliar {
    border: 1px solid white;
    width: 100%;
    border-collapse: separate;
    border-spacing: 1px 1px;
}

</style>

<h2 style="text-align:center; line-height: 80%; margin: 0">
<span style="font-size: 70%">Folha de ativiades do(a) estagiário(a)</span><br>
</h2>

<div style="text-align:justify; font-size: 70%;">
<p style="line-height:100%">
Nome do(a) estudante: <?= $estagiario->aluno_nome; ?> DRE: <?= $estagiario->aluno_registro; ?><br />
Período de realização do estágio: <?= $estagiario->estagiario_periodo; ?><br />
Nível de estágio: <?= $estagiario->estagiario_nivel; ?><br />
Supervisor(a) de campo: <?= $estagiario->supervisor_nome ?? '_________________________' ?>
<span>  </span>CRESS: <?= $estagiario->supervisor_cress ?? '____' ?><br />
Campo de estágio: <?= $estagiario->instituicao_nome ?? '_________________________' ?><br />
Supervisor(a) acadêmico(a): <?= $estagiario->professor_nome ?? '_________________________' ?>
</div>

<div>
<p style='font-size: 100%'>
1º Mês: digitar nome do mês:
</p>
</div>
<table>
    <tr>
        <th style='width: 20%'>
            Data do mês
        </th>
        <th style='width: 20%'>
            Cumpriou quantas horas nessa data?
        </th>
        <th style='width: 60%'>
            Resumo das atividades
        </th>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>

<div style="page-break-after: always;"></div>

<div>
<p style='font-size: 100%'>
2º Mês: digitar nome do mês:
</p>
</div>
<table>
    <tr>
        <th style='width: 20%'>
            Data do mês
        </th>
        <th style='width: 20%'>
            Cumpriou quantas horas nessa data?
        </th>
        <th style='width: 60%'>
            Resumo das atividades
        </th>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>

<div style="page-break-after: always;"></div>

<div>
<p style='font-size: 100%'>
3º Mês: digitar nome do mês:
</p>
</div>
<table>
    <tr>
        <th style='width: 20%'>
            Data do mês
        </th>
        <th style='width: 20%'>
            Cumpriou quantas horas nessa data?
        </th>
        <th style='width: 60%'>
            Resumo das atividades
        </th>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>

<div style="page-break-after: always;"></div>

<div>
<p style='font-size: 100%'>
4º Mês: digitar nome do mês:
</p>
</div>
<table>
    <tr>
        <th style='width: 20%'>
            Data do mês
        </th>
        <th style='width: 20%'>
            Cumpriou quantas horas nessa data?
        </th>
        <th style='width: 60%'>
            Resumo das atividades
        </th>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>

<div style="page-break-after: always;"></div>

<div>
<p style='font-size: 100%'>
5º Mês: digitar nome do mês:
</p>
</div>
<table>
    <tr>
        <th style='width: 20%'>
            Data do mês
        </th>
        <th style='width: 20%'>
            Cumpriou quantas horas nessa data?
        </th>
        <th style='width: 60%'>
            Resumo das atividades
        </th>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>

<p style="font-size: 90%;">
Total de horas do semestre: __________
</p>

<p style="text-align:right">Rio de Janeiro, <?= $dia . ' de ' . $mes . ' de ' . $ano; ?>.</p>

<div>
<p style="text-align:left; line-height:100%; font-size: 90%">
Assinam (este documento só pode ser entregue à Coordenação de Estágio após assinatura das partes abaixo):
</p>
</div>

<br />
<br />

<table class="auxiliar">
<tr class="auxiliar">
<td class="auxiliar"><span style="font-size: 100%; text-decoration: overline"><?= $estagiario->aluno_nome; ?></span></td>
<td class="auxiliar"><span style="font-size: 100%; text-decoration: overline"><?= $estagiario->supervisor_nome; ?></span></td>
</tr>

<tr class="auxiliar">
<td class="auxiliar"><span style="font-size: 100%">DRE: <?= $estagiario->aluno_registro; ?></span></td>
<td class="auxiliar"><span style="font-size: 100%">Supervisão de campo</span><br><span>CRESS: <?= $estagiario->supervisor_cress; ?></span></td>
</tr>
</table>
