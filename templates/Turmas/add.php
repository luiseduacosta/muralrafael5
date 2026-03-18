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
                    <?= $this->Html->link(__('Listar Turma estagios'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($turma) ?>
            <fieldset>
                <h3><?= __('Adicionando Turma estagio') ?></h3>
                <?php
                    echo $this->Form->control('turma');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
