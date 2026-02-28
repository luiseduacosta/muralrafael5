<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Turno> $turnos
 */
?>
<div class="turnos index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Novo Turno'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
    
    <h3><?= __('Turnos') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('turno') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos as $turno): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $turno->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $turno->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $turno->id], ['confirm' => __('Are you sure you want to delete # {0}?', $turno->id)]) ?>
                    </td>
                    <td><?= h($turno->id) ?></td>
                    <td><?= h($turno->turno) ?></td>
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