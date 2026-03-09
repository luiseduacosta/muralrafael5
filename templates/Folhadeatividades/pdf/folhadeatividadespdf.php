<?php
/**
 * Folha de Atividades PDF
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
$this->assign('title', 'Folha de Atividades');

$supervisora = isset($estagiario->supervisor->nome);
if ($supervisora) {
    $supervisora = $estagiario->supervisor->nome;
} else {
    $supervisora = "____________________";
}

$regiao = isset($estagiario->supervisor->regiao);
if ($regiao) {
    $regiao = $estagiario->supervisor->regiao;
} else {
    $regiao = '__';
}

$cress = isset($estagiario->supervisor->cress);
if ($cress) {
    $cress = $estagiario->supervisor->cress;
} else {
    $cress = '_____';
}

$instituicao = isset($estagiario->instituicao->instituicao);
if ($instituicao) {
    $instituicao = $estagiario->instituicao->instituicao;
} else {
    $instituicao = '_______________';
}

$professora = isset($estagiario->professor->nome);
if ($professora) {
    $professora = $estagiario->professor->nome;
} else {
    $professora = '_______________';
}

?>

<h2 style="text-align:center; line-height: 80%; margin: 0">
    <span style="font-size: 100%">Folha de ativiades do(a) estagiário(a) <?= $estagiario->aluno->nome ?></span>
</h2>

<p style="font-size: 90%">DRE: <?= $estagiario->aluno->registro ?>
    Telefone: <?= $estagiario->aluno->celular ?>
    E-mail: <?= $estagiario->aluno->email ?>
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
                <td><?= $estagiario->nivel ?></td>
                <td><?= $estagiario->periodo ?></td>
                <td><?= $instituicao ?></td>
                <td><?= $cress ?></td>
                <td><?= $supervisora ?></td>
                <td><?= $professora ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Atividades de estágio</h2>

    <table class='table table-bordered' style="border: 1px; width: 90%; background-color: white;">
        <thead class="thead-light">
            <tr>
                <th><?= 'Dia'; ?></th>
                <th><?= 'Início'; ?></th>
                <th><?= 'Final'; ?></th>
                <th><?= 'Horas'; ?></th>
                <th><?= 'Atividade'; ?></th>
            </tr>
        </thead>

        <tbody>
            <?php $seconds = NULL; ?>
            <?php foreach ($atividades as $atividade): ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($atividade->dia)); ?>&nbsp;</td>
                    <td><?php echo $atividade->inicio; ?>&nbsp;</td>
                    <td><?php echo $atividade->final; ?>&nbsp;</td>               
                    <td><?php echo $atividade->horario; ?>&nbsp;</td>
                    <td><?php echo $atividade->atividade; ?>&nbsp;</td>
                </tr>
                <?php
                list($hour, $minute, $second) = array_pad(explode(':', $atividade->horario), 3, null);
                $seconds += (int)$hour * 3600;
                $seconds += (int)$minute * 60;
                $seconds += (int)$second;
                // pr($seconds);
                ?>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr class="table-info">
                <th colspan="3">Total de horas: </th>
                <th>
                    <?php
                    $hours = floor($seconds / 3600);
                    $seconds -= $hours * 3600;
                    $minutes = floor($seconds / 60);
                    $seconds -= $minutes * 60;
                    echo $hours . ":" . $minutes . ":" . $seconds;
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
            <td style="width: 33%"><span style="font-size: 100%; text-decoration: overline"><?= $estagiario->aluno->nome ?></span></td>
            <td style="width: 33%"><span style="font-size: 100%; text-decoration: overline"><?= $supervisora ?></span></td>
        </tr>

        <tr>
            <td style="width: 33%"></td>
            <td style="width: 33%"><span style="font-size: 100%">DRE: <?= $estagiario->aluno->registro ?></span></td>
            <td style="width: 33%"><span style="font-size: 100%">CRESS <?= $regiao ?>ª Região <?= $cress ?></span></td>
        </tr>
    </table>
</div>
