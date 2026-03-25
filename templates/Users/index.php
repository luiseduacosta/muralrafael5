<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\User> $users
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Load CSS bootstrap to the datatables //-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="users index content">
    <aside>
        <?php if ($user_data['administrador_id']) : ?>
            <div class="nav">
                <?= $this->Html->link(__('Novo usuário'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
        <?php endif; ?>
    </aside>
    
    <h3><?= __('Lista de usuários') ?></h3>
    
    <div class="table_wrap">
        <table class="table table-striped table-hover table-bordered" id="table-users">
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th>Id</th>
                    <th>Email</th>
                    <th>Categoria</th>
                    <th>Criado</th>
                    <th>Modificado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                <tr>
                    <td class="actions">
                        <?php if ($user_data['administrador_id']) : ?>
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id]) ?>
                            <?php if ($user->id !== $user_session->id) : ?>
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
                            '4' => 'Supervisor',
                        ];
                        echo $categorias[$user->categoria] ?? $user->categoria;
                        ?>
                    </td>
                    <td><?= $user->created ? $user->created->format('d/m/Y H:i:s') : '' ?></td>
                    <td><?= $user->modified ? $user->modified->format('d/m/Y H:i:s') : '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table-users').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            }
        });
    });
</script>
