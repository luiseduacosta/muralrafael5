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
        <div class="turmas form content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $turma->id],
                            ['confirm' => __('Are you sure you want to delete {0}?', $turma->turma), 'class' => 'button'],
                        ) ?>
                    <?php endif; ?>
                    <?= $this->Html->link(__('Listar Turma estagios'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($turma) ?>
            <fieldset>
                <h3><?= __('Editando turma_') . $turma->id ?></h3>
                <?php
                    echo $this->Form->control('turma', ['label' => 'Nome da Turma']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
