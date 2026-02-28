<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visita[]|\Cake\Collection\CollectionInterface $visitas
 */
?>
<div class="visitas index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Nova Visita'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
    
    <h3><?= __('Lista de visitas') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('instituicao_id') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th><?= $this->Paginator->sort('motivo') ?></th>
                    <th><?= $this->Paginator->sort('responsavel') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitas as $visita): ?>
                <tr> 
                <?php //pr($visita); ?>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $visita->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $visita->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $visita->id], ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$visita->id, ['action' => 'view', $visita->id]) ?></td>
                    <td><?= $visita->instituicao ? $this->Html->link($visita->instituicao->instituicao, ['controller' => 'Visitas', 'action' => 'view', $visita->id]) : '' ?></td>
                    <td><?= h($visita->data) ?></td>
                    <td><?= h($visita->motivo) ?></td>
                    <td><?= h($visita->responsavel) ?></td>
                    <td><?= h($visita->avaliacao) ?></td>
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
