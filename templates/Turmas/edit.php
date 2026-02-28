<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turma $turma
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="turmas form content">
            <aside>
                <div class="nav">
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $turma->id],
                        ['confirm' => __('Are you sure you want to delete {0}?', $turma->turma), 'class' => 'button']
                    ) ?>
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
