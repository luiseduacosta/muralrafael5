<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
 */

namespace App\View\PDF; 
use Cake\I18n\I18n;
use Cake\I18n\Timezone;
use Cake\I18n\FrozenDate;

I18n::setLocale('pt-BR');
$hoje = FrozenDate::now('America/Sao_Paulo', 'pt_BR');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->layout = 'default';
$this->assign('title', 'Lista de Inscrições');

?>

<h1>Lista de Inscrições</h1>
<table>
    <tr>
        <th>Nome do Aluno</th>
        <th>Matrícula</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Data de Inscrição</th>
    </tr>
    <?php foreach ($inscricao->inscricoes as $insc): ?>
    <tr>
        <td><?= $insc->aluno->nome ?></td>
        <td><?= $insc->aluno->registro ?></td>
        <td><?= $insc->aluno->email ?></td>
        <td><?= $insc->aluno->telefone ?></td>
        <td><?= $insc->timestamp->format('d/m/Y H:i:s') ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<p>
    Gerado em <?= $dia ?> de <?= $mes ?> de <?= $ano ?>
</p>
