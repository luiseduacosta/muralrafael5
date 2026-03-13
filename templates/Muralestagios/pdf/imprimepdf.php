<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muralestagio $muralestagio
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
$this->assign('title', 'Lista de Inscrições');

?>

<div style="text-align: center;">
<h1>Seleção de estágio para <?= $muralestagio->instituicao ?><br />
Lista de inscrições</h1>
</div>

<table>
    <tr>
        <th>Aluno(a)</th>
        <th>Matrícula</th>
        <th>Email</th>
        <th>Celular</th>
        <th>Data de inscrição</th>
    </tr>
    <?php foreach ($muralestagio->inscricoes as $insc): ?>
    <tr>
        <td><?= $insc->aluno->nome ?? 'S/d' ?></td>
        <td><?= $insc->aluno->registro  ?? 'S/d' ?></td>
        <td><?= $insc->aluno->email ?? 'S/d' ?></td>
        <td><?= $insc->aluno->celular ?? 'S/d' ?></td>
        <td><?= $insc->timestamp->format('d/m/Y H:i:s') ?? 'S/d' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<p style="text-align: right;">
    Rio de Janeiro, <?= $dia ?> de <?= $mes ?> de <?= $ano ?>
</p>

<p style="text-align: center;">
Coordenação de Estágio<br />
ESS/UFRJ
</p>
