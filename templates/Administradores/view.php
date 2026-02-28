<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrador $administrador
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="administradores view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Administradores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Administrador'), ['action' => 'edit', $administrador->id], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>admin_<?= h($administrador->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($administrador->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($administrador->nome) ?></td>
                </tr>
            </table>
            
            <?php if (!empty($administrador->user)) : ?>
            <div class="related">
                <h4><?= __('Related User') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Data') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Users', 'action' => 'view', $administrador->user->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Users', 'action' => 'edit', $administrador->user->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Users', 'action' => 'delete', $administrador->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $administrador->user->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$administrador->user->id, ['controller' => 'Users', 'action' => 'view', $administrador->user->id]) ?></td>
                            <td><?= $administrador->user->email ? $this->Text->autoLinkEmails($administrador->user->email) : '' ?></td>
                            <td><?= h($administrador->user->timestamp) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
