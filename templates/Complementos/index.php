<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Complemento[]|\Cake\Collection\CollectionInterface $complementos
 */
?>
<div class="complementos index content">
    <aside>
		<div class="nav">
            <?= $this->Html->link(__('Novo Complemento'), ['action' => 'add'], ['class' => 'button']) ?>
        </div>
	</aside>
    
    <h3><?= __('Lista de Complementos') ?></h3>
	
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('periodo_especial') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complementos as $complemento): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $complemento->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $complemento->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $complemento->id], ['confirm' => __('Are you sure you want to delete {0}?', $complemento->nome)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$complemento->id, ['action' => 'view', $complemento->id]) ?></td>
                    <td><?= $this->Html->link($complemento->periodo_especial, ['action' => 'view', $complemento->id]) ?></td>
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
