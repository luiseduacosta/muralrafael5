<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Area[]|\Cake\Collection\CollectionInterface $areas
 */
?>
<div class="areas index content">
    <aside>
		<div class="nav">
            <?= $this->Html->link(__('Nova Area'), ['action' => 'add'], ['class' => 'button']) ?>
        </div>
	</aside>
    
    <h3><?= __('Lista de Áreas') ?></h3>
	
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('area') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areas as $area): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $area->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $area->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $area->id], ['confirm' => __('Are you sure you want to delete {0}?', $area->area)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$area->id, ['action' => 'view', $area->id]) ?></td>
                    <td><?= $this->Html->link($area->area, ['action' => 'view', $area->id]) ?></td>
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
