<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turma[]|\Cake\Collection\CollectionInterface $turmas
 */
?>
<div class="turmas index content">
    
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Nova Turma'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
            
    <h3><?= __('Lista de Turmas') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('turma') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turmas as $turma): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $turma->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $turma->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $turma->id], ['confirm' => __('Are you sure you want to delete {0}?', $turma->turma)]) ?>
                    </td>
                    <td><?= h($turma->id) ?></td>
                    <td><?= $this->Html->link(h($turma->turma), ['action' => 'view', $turma->id]) ?></td>
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
