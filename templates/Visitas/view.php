<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visita $visita
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="visitas view content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Editar Visita'), ['action' => 'edit', $visita->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Visita'), ['action' => 'delete', $visita->id], ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Nova Visita'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?php endif; ?>
                    <?= $this->Html->link(__('Listar Visitas'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>visita_<?= h($visita->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($visita->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituição') ?></th>
                    <td><?= $visita->instituicao ? $this->Html->link(h($visita->instituicao->instituicao), ['controller' => 'Instituicoes', 'action' => 'view', $visita->instituicao->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Motivo') ?></th>
                    <td><?= h($visita->motivo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Responsável') ?></th>
                    <td><?= h($visita->responsavel) ?></td>
                </tr>
                <tr>
                    <th><?= __('Avaliação') ?></th>
                    <td><?= h($visita->avaliacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data') ?></th>
                    <td><?= empty($visita->data) ? '' : $visita->data->format('d/m/Y') ?></td>
                </tr>
            </table>
            <div class="text">
                <h2><?= __('Descrição') ?></h2>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($visita->descricao)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
