<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<div class="users form content">
    <aside>
        <div class="nav">
            <?php if ($user_data['administrador_id']): ?>
                <?= $this->Html->link(__('Listar Usuários'), ['action' => 'index'], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if (!$user_session):  ?>
                <?= $this->Html->link(__('Fazer Login'), ['action' => 'login'], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <h3><?= __('Adicionando usuário') ?></h3>
        <?php
            echo $this->Form->control('email', ['required' => true, 'autocomplete' => 'username']);
            echo $this->Form->control('password', ['label' => 'Senha', 'required' => true, 'autocomplete' => 'new-password', 'id' => 'password' ]);
            echo $this->element('show_password');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
    <?= $this->Form->end() ?>
</div>
