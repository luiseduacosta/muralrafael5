<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrador[]|\Cake\Collection\CollectionInterface $administradores
 */
?>
<div class="administradores index content">
    
    <h3><?= __('Lista de Administradores') ?></h3>
	
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($administradores as $administrador): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $administrador->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $administrador->id]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$administrador->id, ['action' => 'view', $administrador->id]) ?></td>
                    <td><?= $this->Html->link($administrador->nome, ['action' => 'view', $administrador->id]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>
