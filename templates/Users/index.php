<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="users index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Novo usuário'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
    
    <h3><?= __('Lista de usuários') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <!--th><?= h('Categorias') ?></th-->
                    <th><?= $this->Paginator->sort('created', 'Criado') ?></th>
                    <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $user->id)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$user->id, ['action' => 'view', $user->id]) ?></td>
                    <td><?= $user->email ? $this->Text->autoLinkEmails($user->email) : '' ?></td>
                    <!--td>
                    <?php 
                        $categorias = [];
                        if ($user->administrador) $categorias[] = 'administrador';
                        if ($user->aluno) $categorias[] = 'aluno';
                        if ($user->professor) $categorias[] = 'professor';
                        if ($user->supervisor) $categorias[] = 'supervisor';
                        echo implode(', ', $categorias);
                    ?>
                    </td-->
                    <td><?= $user->created ? h($user->created) : '' ?></td>
                    <td><?= $user->modified ? h($user->modified) : '' ?></td>
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
