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
$this->assign('title', 'Nota e CH');
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
    }
</style>

<h3>Estagiários - Lançamento de CH e Nota<br />
Professor: <?= h($professor->nome) ?><br />
Período: <?= h($periodo) ?></h3>

<div style="text-align: right;">
<p>Rio de Janeiro, <?= h($dia . ' de ' . $mes . ' de ' . $ano) ?></p>
</div>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Supervisor</th>
            <th>Instituição</th>
            <th>Nível</th>
            <th>Nota</th>
            <th>CH</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($estagiarios as $estagiario): ?>
        <tr>
            <td><?= h($estagiario->aluno->nome ?? '') ?></td>
            <td><?= h($estagiario->supervisor->nome ?? '') ?></td>
            <td><?= h($estagiario->instituicao->instituicao ?? '') ?></td>
            <td><?= h($estagiario->nivel ?? '') ?></td>
            <td><?= h($estagiario->nota ?? '') ?></td>
            <td><?= h($estagiario->ch ?? '') ?></td>
        </tr>
<?php endforeach; ?>
</table>