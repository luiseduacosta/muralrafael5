<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Questionario $questionario
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<?= $this->element('templates') ?>

<div class="container mt-1">
    <nav class="nav navbar-expand-lg navbar-light bg-light w-75 mx-auto" id="actions-sidebar">
        <ul class="navbar-nav collapse navbar-collapse">
            <?php if ($user_data['administrador_id']): ?>
            <li class="nav-item">
                <?= $this->Form->postLink(
                    __('Excluir'),
                    ['controller' => 'Questionarios', 'action' => 'delete', $questionario->id],
                    ['confirm' => __('Tem certeza que deseja excluir este registo # {0}?', $questionario->id), 'class' => 'btn btn-danger me-1', 'style' => 'font-size: 10pt;']
                ) ?>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <?= $this->Html->link(__('Listar'), ['controller' => 'Questionarios', 'action' => 'index'], ['class' => 'btn btn-primary me-1', 'style' => 'font-size: 10pt;']) ?>
            </li>
            </li>
        </ul>
    </nav>

    <div class="container mt-1">
        <?= $this->Form->create($questionario) ?>
        <fieldset>
            <legend><?= __('Editar questionário') ?></legend>
            <?php
            echo $this->Form->control('title');
            echo $this->Form->control('description');
            echo $this->Form->control('is_active');
            echo $this->Form->control('category');
            echo $this->Form->control('target_user_type');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Confirma'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>