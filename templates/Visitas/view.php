<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visita $visita
 */
//pr($visita);
?>
<div>
    <div class="column-responsive column-80">
        <div class="visitas view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Editar Visita'), ['action' => 'edit', $visita->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Visita'), ['action' => 'delete', $visita->id], ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Listar Visitas'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Nova Visita'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>visita_<?= h($visita->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($visita->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituicao') ?></th>
                    <td><?= $visita->instituicao ? $this->Html->link(h($visita->instituicao->instituicao), ['controller' => 'Instituicoes', 'action' => 'view', $visita->instituicao->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Motivo') ?></th>
                    <td><?= h($visita->motivo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Professor') ?></th>
                    <td><?= $visita->professor ? $this->Html->link(h($visita->professor->nome), ['controller' => 'Professores', 'action' => 'view', $visita->professor->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Avaliacao') ?></th>
                    <td><?= h($visita->avaliacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data') ?></th>
                    <td><?= h($visita->data) ?></td>
                </tr>
            </table>
            <div class="text">
                <h2><?= __('Descricao') ?></h2>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($visita->descricao)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
