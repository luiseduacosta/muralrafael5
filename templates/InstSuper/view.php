<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\InstSuper $instSuper
 */
?>

<div class="container">

        <h3><?= __('Instituição-Supervisor') ?></h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($instSuper->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituição') ?></th>
                    <td><?= $instSuper->has('instituicao') ? h($instSuper->instituicao->instituicao) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Supervisor') ?></th>
                    <td><?= $instSuper->has('supervisor') ? h($instSuper->supervisor->nome) : '' ?></td>
                </tr>
            </table>
        </div>
</div>

    <div class="actions">
        <?= $this->Html->link(__('Editar Instituição-Supervisor'), ['action' => 'edit', $instSuper->id], ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->postLink(__('Excluir Instituição-Supervisor'), ['action' => 'delete', $instSuper->id], ['class' => 'btn btn-danger', 'confirm' => __('Tem certeza que deseja excluir o registro instituicao-supervisor {0}?', $instSuper->id)]) ?>
        <?= $this->Html->link(__('Listar Instituição-Supervisor'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
