<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Categoria $categoria
 */
?>

<div class="container">

        <h3><?= h($categoria->categoria) ?></h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($categoria->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Categoria') ?></th>
                    <td><?= h($categoria->categoria) ?></td>
                </tr>
            </table>
        </div>
</div>

    <div class="actions">
        <?= $this->Html->link(__('Editar Categoria'), ['action' => 'edit', $categoria->id], ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->postLink(__('Excluir Categoria'), ['action' => 'delete', $categoria->id], ['class' => 'btn btn-danger', 'confirm' => __('Tem certeza que deseja excluir a categoria {0}?', $categoria->id)]) ?>
        <?= $this->Html->link(__('Listar Categorias'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
