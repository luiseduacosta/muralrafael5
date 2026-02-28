<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Complemento $complemento
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="complementos view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Complementos'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Complemento'), ['action' => 'edit', $complemento->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Complemento'), ['action' => 'delete', $complemento->id], ['confirm' => __('Are you sure you want to delete {0}?', $complemento->periodo_especial), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Novo Complemento'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>complemento_<?= h($complemento->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($complemento->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($complemento->periodo_especial) ?></td>
                </tr>
            </table>
            
        </div>
    </div>
</div>
