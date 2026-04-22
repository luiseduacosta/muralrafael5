<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Categoria[]|\Cake\Collection\CollectionInterface $categorias
 */
?>

<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= __('Categorias de usuários') ?></h3>
        <?= $this->Html->link(__('Nova Categoria'), ['action' => 'add'], ['class' => 'btn btn-success btn-sm me-2', 'style' => 'font-size: 10pt;']) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('categoria') ?></th>
                    <th class="actions"><?= __('Ações') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?= $this->Number->format($categoria->id) ?></td>
                    <td><?= h($categoria->categoria) ?></td>
                    <td class="d-flex gap-2">
                        <?= $this->Html->link(__('Visualizar'), ['action' => 'view', $categoria->id], ['class' => 'btn btn-info btn-sm']) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $categoria->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $categoria->id], ['class' => 'btn btn-danger btn-sm', 'confirm' => __('Tem certeza que deseja excluir a categoria {0}?', $categoria->id)]) ?>
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
