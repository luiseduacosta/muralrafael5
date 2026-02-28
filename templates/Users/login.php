<div class="users form content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Novo usuário'), ['action' => 'add'], ['class' => 'button']) ?>
        </div>
    </aside>
    <?= $this->Form->create() ?>
    <fieldset>
        <h3><?= __('Fazer login') ?></h3>
        <?= $this->Form->control('email', ['autocomplete' => 'current-username']) ?>
        <?= $this->Form->control('password', ['autocomplete' => 'current-password']) ?>
    </fieldset>
    <?= $this->Form->button(__('Login'), ['class' => 'button btn-info']); ?>
    <?= $this->Form->end() ?>
</div>