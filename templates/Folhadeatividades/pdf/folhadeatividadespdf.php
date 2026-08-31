<?php
/**
 * Folha de Atividades PDF
 *
 * @var \App\Model\Entity\Estagiario $estagiario
 * @var iterable<\App\Model\Entity\Folhadeatividade> $atividades
 */
namespace App\View\PDF;

use Cake\I18n\Date;
use Cake\I18n\I18n;

I18n::setLocale('pt-BR');
$hoje = Date::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->layout = 'default';
$this->assign('title', 'Folha de Atividades');

$supervisora = isset($estagiario->supervisor->nome) ? $estagiario->supervisor->nome : '____________________';
$regiao = isset($estagiario->supervisor->regiao) ? $estagiario->supervisor->regiao : '__';
$cress = isset($estagiario->supervisor->cress) ? $estagiario->supervisor->cress : '_____';
$instituicao = isset($estagiario->instituicao->instituicao) ? $estagiario->instituicao->instituicao : '_______________';
$professora = isset($estagiario->professor->nome) ? $estagiario->professor->nome : '_______________';
?>

<h2 style="text-align:center; line-height: 80%; margin: 0">
    <span style="font-size: 100%">Folha de atividades do(a) estagiário(a) <?= h($estagiario->aluno->nome ?? '') ?></span>
</h2>

<p style="font-size: 90%">DRE: <?= h($estagiario->aluno->registro ?? '') ?>
    Telefone: <?= h($estagiario->aluno->celular ?? '') ?>
    E-mail: <?= h($estagiario->aluno->email ?? '') ?>
</p>

<div class="container">
    <table class='table table-bordered' style="border: 1px; width: 90%; background-color: white;">
        <thead class='thead-light'>
            <tr>
                <th class="text-center">Nível</th>
                <th class="text-center">Período</th>
                <th class="text-center">Instituição</th>
                <th class="text-center">CRESS</th>
                <th class="text-center">Supervisor</th>
                <th class="text-center">Professor(a)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= h($estagiario->nivel) ?></td>
                <td><?= h($estagiario->periodo) ?></td>
                <td><?= h($instituicao) ?></td>
                <td><?= h($cress) ?></td>
                <td><?= h($supervisora) ?></td>
                <td><?= h($professora) ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Atividades de estágio</h2>

    <table class='table table-bordered' style="border: 1px; width: 90%; background-color: white;">
        <thead class="thead-light">
            <tr>
                <th>Dia</th>
                <th>Início</th>
                <th>Final</th>
                <th>Horas</th>
                <th>Atividade</th>
            </tr>
        </thead>

        <tbody>
            <?php $seconds = 0; ?>
            <?php foreach ($atividades as $atividade): ?>
                <tr>
                    <td><?= !empty($atividade->dia) ? date('d-m-Y', strtotime((string)$atividade->dia)) : '' ?>&nbsp;</td>
                    <td><?= h($atividade->inicio) ?>&nbsp;</td>
                    <td><?= h($atividade->final) ?>&nbsp;</td>
                    <td><?= h($atividade->horario) ?>&nbsp;</td>
                    <td><?= h($atividade->atividade) ?>&nbsp;</td>
                </tr>
                <?php
                if (!empty($atividade->horario)) {
                    [$hour, $minute, $second] = array_pad(explode(':', (string)$atividade->horario), 3, '0');
                    $seconds += (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                }
                ?>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr class="table-info">
                <th colspan="3">Total de horas: </th>
                <th>
                    <?php
                    $hours = floor($seconds / 3600);
                    $remSeconds = $seconds % 3600;
                    $minutes = floor($remSeconds / 60);
                    $remSeconds = $remSeconds % 60;
                    echo sprintf('%02d:%02d:%02d', $hours, $minutes, $remSeconds);
                    ?>
                </th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:right; line-height:100%;">
        Rio de Janeiro, <?= $dia . ' de ' . $mes . ' de ' . $ano; ?>.
    </p>

    <br />
    <br />
    <br />

    <table class="table" style="width: 100%; background-color: white;">
        <tr>
            <td style="width: 33%"><span style="font-size: 100%; text-decoration: overline">Coordenação de Estágio</span></td>
            <td style="width: 33%"><span style="font-size: 100%; text-decoration: overline"><?= h($estagiario->aluno->nome ?? '') ?></span></td>
            <td style="width: 33%"><span style="font-size: 100%; text-decoration: overline"><?= h($supervisora) ?></span></td>
        </tr>

        <tr>
            <td style="width: 33%"></td>
            <td style="width: 33%"><span style="font-size: 100%">DRE: <?= h($estagiario->aluno->registro ?? '') ?></span></td>
            <td style="width: 33%"><span style="font-size: 100%">CRESS <?= h($regiao) ?>ª Região <?= h($cress) ?></span></td>
        </tr>
    </table>
</div>
