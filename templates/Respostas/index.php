<?php
declare(strict_types=1);

use Cake\I18n\Time;
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Resposta> $respostas
 */

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<?= $this->element('templates') ?>

<div class="container mt-1">

    <nav class="navbar navbar-expand-lg navbar-light bg-light w-75 mx-auto" id="actions-sidebar">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#actions-sidebar" aria-controls="actions-sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav mr-auto">
            <?php if ($user_data['administrador_id']): ?>
            <li class="nav-item">
                <?= $this->Html->link(__('Nova resposta'), ['action' => 'add'], ['class' => 'btn btn-primary', 'style' => 'font-size: 10pt;']) ?>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <h3><?= __('Respostas') ?></h3>
    <div class="container mt-4">
        <table class="table table-striped table-hover table-responsive">
            <thead class="thead-light">
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('estagiario.aluno.nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('estagiario_id', 'Nível de estágio') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= __('Ações') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($respostas as $resposta): ?>
                    <tr>
                        <td><?= $this->Number->format($resposta->id) ?></td>
                        <td><?= $this->Html->link($resposta->estagiario->aluno->nome, ['controller' => 'Respostas', 'action' => 'view', $resposta->id]) ?>
                        </td>
                        <td><?= $resposta->hasValue('estagiario') ? $this->Html->link($resposta->estagiario->nivel, ['controller' => 'Estagiarios', 'action' => 'view', $resposta->estagiario->id]) : '' ?>
                        </td>
                        <td><?= $this->Time->format($resposta->created, 'd-MM-Y HH:mm:ss') ?></td>
                        <td><?= $this->Time->format($resposta->modified, 'd-MM-Y HH:mm:ss') ?></td>
                        <td class="d-grid">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $resposta->id], ['class' => 'btn btn-primary btn-sm btn-block p-1 mb-1']) ?>
                            <?php if ($user_data['administrador_id']): ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $resposta->id], ['class' => 'btn btn-primary btn-sm btn-block p-1 mb-1']) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $resposta->id], ['confirm' => __('Tem certeza que deseja excluir este registo # {0}?', $resposta->id), 'class' => 'btn btn-danger btn-sm btn-block p-1 mb-1']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $this->element('paginator') ?>

</div>