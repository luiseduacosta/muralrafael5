<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

declare(strict_types=1);
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div class="users index content">
	<aside>
        <?php if ($user_data['administrador_id']): ?>
            <div class="nav">
                <?= $this->Html->link(__('Novo usuário'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
        <?php endif; ?>
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
                    <th><?= $this->Paginator->sort('categoria', 'Categorias') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Criado') ?></th>
                    <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="actions">
                        <?php if ($user_data['administrador_id']): ?>
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id]) ?>
                            <?php if ($user->id !== $user_session->id): ?>
                                <?= $this->Html->link(__('Alternar'), ['action' => 'alternarusuario', $user->id]) ?>
                            <?php endif; ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $user->id)]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Html->link((string)$user->id, ['action' => 'view', $user->id]) ?></td>
                    <td><?= $user->email ? $this->Text->autoLinkEmails($user->email) : '' ?></td>
                    <td>
                    <?php
                        $categorias = [
                            '1' => 'Administrador',
                            '2' => 'Aluno',
                            '3' => 'Professor',
                            '4' => 'Supervisor'
                        ];
                        echo isset($categorias[$user->categoria]) ? $categorias[$user->categoria] : $user->categoria;
                    ?>
                    </td>
                    <td><?= $user->created ? h($user->created->format('d/m/Y H:i:s')) : '' ?></td>
                    <td><?= $user->modified ? h($user->modified->format('d/m/Y H:i:s')) : '' ?></td>
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
