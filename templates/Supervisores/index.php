<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Supervisor> $supervisores
 */
?>
<div class="supervisores index content">

<aside>
    <aside class="side-nav">
        <div class="row">
            <div class="col-6 d-flex justify-content-start">
                <?= $this->Html->link(__('Novo(a) Supervisor(a)'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
            <div class="col-6 d-flex justify-content-end">
                <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'class' => 'form-inline']) ?>
                    <div class="form-group">
                        <?= $this->Form->label('busca', 'Busca', ['class' => 'button mr-2 mb-4']) ?>
                        <?= $this->Form->control('busca', ['placeholder' => 'Busca supervisor(a)', 'label' => false, 'onKeyDown' => 'if (event.keyCode == 13) {this.form.submit();}', 'class' => 'form-control']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </aside>

    <h3><?= __('Lista de Supervisores(as)') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div>
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('cpf', 'CPF') ?></th>
                    <th><?= $this->Paginator->sort('email', 'Email') ?></th>
                    <th><?= $this->Paginator->sort('cress', 'CRESS') ?></th>
                    <th><?= $this->Paginator->sort('estagiarios_count', 'Estagiarios') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supervisores as $supervisor) : ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $supervisor->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $supervisor->id]) ?>
                        <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $supervisor->id], ['confirm' => __('Are you sure you want to delete {0}?', $supervisor->nome)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$supervisor->id, ['action' => 'view', $supervisor->id]) ?></td>
                    <td><?= $this->Html->link($supervisor->nome, ['action' => 'view', $supervisor->id]) ?></td>
                    <td><?= h($supervisor->cpf) ?></td>
                    <td><?= $supervisor->email ? $this->Text->autoLinkEmails($supervisor->email) : '' ?></td>
                    <td><?= h($supervisor->cress) ?></td>
                    <td><?= h($supervisor->estagiarios_count) ?></td>
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
