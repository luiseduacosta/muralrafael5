<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turma $turma
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

?>
<div>
    <div class="column-responsive column-80">
        <div class="turmas view content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Listar Turmas'), ['action' => 'index'], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Editar Turma'), ['action' => 'edit', $turma->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Turma'), ['action' => 'delete', $turma->id], ['confirm' => __('Are you sure you want to delete {0}?', $turma->turma), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Nova Turma'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3><?= h($turma->turma) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($turma->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Turma') ?></th>
                    <td><?= h($turma->turma) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
