<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\InstSuper> $instSuper
 */
?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= __('Instituição-Supervisor') ?></h3>
        <?= $this->Html->link(__('Nova Instituição-Supervisor'), ['action' => 'add'], ['class' => 'btn btn-success btn-sm me-2', 'style' => 'font-size: 10pt;']) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('instituicao_id') ?></th>
                    <th><?= $this->Paginator->sort('supervisor_id') ?></th>
                    <th class="actions"><?= __('Ações') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instSuper as $instSuperItem) : ?>
                <tr>
                    <td><?= $this->Number->format($instSuperItem->id) ?></td>
                    <td><?= $instSuperItem->has('instituicao') ? h($instSuperItem->instituicao->instituicao) : '' ?></td>
                    <td><?= $instSuperItem->has('supervisor') ? h($instSuperItem->supervisor->nome) : '' ?></td>
                    <td class="d-flex gap-2">
                        <?= $this->Html->link(__('Visualizar'), ['action' => 'view', $instSuperItem->id], ['class' => 'btn btn-info btn-sm']) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $instSuperItem->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $instSuperItem->id], ['class' => 'btn btn-danger btn-sm', 'confirm' => __('Tem certeza que deseja excluir o registro instituicao-supervisor {0}?', $instSuperItem->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeira')) ?>
            <?= $this->Paginator->prev('< ' . __('anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('próxima') . ' >') ?>
            <?= $this->Paginator->last(__('última') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}')) ?></p>
    </div>
</div>
