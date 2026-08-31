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
            <?php if ($isAdmin) : ?>
                <?= $this->Html->link(__('Listar Usuários'), ['action' => 'index'], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if (!$user_session) : ?>
                <?= $this->Html->link(__('Fazer Login'), ['action' => 'login'], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <h3><?= __('Adicionando usuário') ?></h3>
        <?php
        if ($isAdmin) {
            echo $this->Form->control('categoria', [
                'options' => [
                    '1' => 'Administrador',
                    '2' => 'Aluno',
                    '3' => 'Professor',
                    '4' => 'Supervisor',
                ],
            ]);
        } else {
            echo $this->Form->control('categoria', [
                'options' => [
                    '0' => 'Selecione',
                    '2' => 'Aluno',
                    '3' => 'Professor',
                    '4' => 'Supervisor',
                ],
            ]);
        }
        echo $this->Form->control('identificacao', [
            'label' => 'DRE, Siape ou CRESS',
            'required' => true,
        ]);
        echo $this->Form->control('email', [
            'required' => true,
            'autocomplete' => 'username',
        ]);
        echo $this->Form->control('nome', [
            'label' => 'Nome completo',
            'required' => false,
        ]);
        echo $this->Form->control('password', [
            'label' => 'Senha',
            'required' => true,
            'autocomplete' => 'new-password',
            'id' => 'password',
        ]);
        echo $this->element('show_password');
        if ($isAdmin) {
            echo $this->Form->control('ativo');
            echo $this->Form->control('role', [
                'options' => [
                    'admin' => 'Admin',
                    'aluno' => 'Aluno',
                    'professor' => 'Professor',
                    'supervisor' => 'Supervisor',
                ],
                'empty' => '(derivado da categoria)',
            ]);
        }
        ?>
    </fieldset>
    <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
    <?= $this->Form->end() ?>
</div>
