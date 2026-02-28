<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turno $turno
 */
?>
<div class="row">
    <div class="column column-80">
        <div class="turnos view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Turnos'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Turno'), ['action' => 'edit', $turno->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Turno'), ['action' => 'delete', $turno->id], ['confirm' => __('Are you sure you want to delete # {0}?', $turno->id), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Novo Turno'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>turno_<?= h($turno->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($turno->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Turno') ?></th>
                    <td><?= h($turno->turno) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>