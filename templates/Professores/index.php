<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor[]|\Cake\Collection\CollectionInterface $professores
 */
?>
<div class="professores index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Novo Professor'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
    
    <h3><?= __('Lista de Professores') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('telefone') ?></th>
                    <th><?= $this->Paginator->sort('celular') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('curriculolattes') ?></th>
                    <th><?= $this->Paginator->sort('departamento') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($professores as $professor): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $professor->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $professor->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $professor->id], ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$professor->id, ['action' => 'view', $professor->id]) ?></td>
                    <td><?= $this->Html->link(h($professor->nome), ['action' => 'view', $professor->id]) ?></td>
                    <td><?= $professor->telefone ? '(' . h($professor->ddd_telefone) . ')' . h($professor->telefone) : '' ?></td>
                    <td><?= $professor->celular ? '(' . h($professor->ddd_celular) . ')' . h($professor->celular) : '' ?></td>
                    <td><?= ($professor->user and $professor->user->email) ? $this->Text->autoLinkEmails($professor->user->email) : '' ?></td>
                    <td><?= $professor->curriculolattes ? $this->Html->link('http://lattes.cnpq.br/' . h($professor->curriculolattes)) : '' ?></td>
                    <td><?= h($professor->departamento) ?></td>
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
