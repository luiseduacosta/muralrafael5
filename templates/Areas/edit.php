<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Area $area
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div class="areas form content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Listar Áreas'), ['action' => 'index'], ['class' => 'button']) ?>
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Form->postLink(
                    __('Excluir'),
                    ['action' => 'delete', $area->id],
                    ['confirm' => __('Are you sure you want to delete {0}?', $area->area), 'class' => 'button'],
                ) ?>
            <?php endif; ?>
        </div>
    </aside>
    <?= $this->Form->create($area) ?>
    <fieldset>
        <h3><?= __('Editando area_' . $area->id) ?></h3>
        <?php
            echo $this->Form->control('area');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
    <?= $this->Form->end() ?>
</div>
