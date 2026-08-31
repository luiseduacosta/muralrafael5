<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
$isAdmin = !empty($user_data['administrador_id']);
?>

<div class="users form content">
    <aside>
        <div class="nav">
            <?php
            echo $this->Html->link(__('Ver Usuário'), ['action' => 'view', $user->id], ['class' => 'button']);
            if ($isAdmin) :
                echo $this->Html->link(__('Listar Usuários'), ['action' => 'index'], ['class' => 'button']);
                echo $this->Form->postLink(
                    __('Excluir'),
                    ['action' => 'delete', $user->id],
                    ['confirm' => __('Are you sure you want to delete user_{0}?', $user->email), 'class' => 'button'],
                );
            endif;
            ?>
        </div>
    </aside>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <h3><?= __('Editando user_') . $user->id ?></h3>
        <?php
            echo $this->Form->control('email', [
                'type' => 'email',
                'label' => 'Novo email',
            ]);
            echo $this->Form->control('nome', [
                'label' => 'Nome completo',
            ]);
            if ($isAdmin) {
                echo $this->Form->control('ativo');
                echo $this->Form->control('role', [
                    'options' => [
                        'admin' => 'Admin',
                        'aluno' => 'Aluno',
                        'professor' => 'Professor',
                        'supervisor' => 'Supervisor',
                    ],
                ]);
                echo $this->Form->control('categoria', [
                    'options' => [
                        '1' => 'Administrador',
                        '2' => 'Aluno',
                        '3' => 'Professor',
                        '4' => 'Supervisor',
                    ],
                ]);
                echo $this->Form->control('identificacao', [
                    'label' => 'DRE, Siape ou CRESS',
                ]);
            }
            ?>
    </fieldset>
    <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
    <?= $this->Form->end() ?>
</div>
