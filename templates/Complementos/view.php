<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Complemento $complemento
 */
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="complementos view content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Listar Complementos'), ['action' => 'index'], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Editar Complemento'), ['action' => 'edit', $complemento->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Complemento'), ['action' => 'delete', $complemento->id], ['confirm' => __('Are you sure you want to delete {0}?', $complemento->periodo_especial), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Novo Complemento'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3>complemento_<?= h($complemento->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($complemento->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($complemento->periodo_especial) ?></td>
                </tr>
            </table>
            
        </div>
    </div>
</div>
