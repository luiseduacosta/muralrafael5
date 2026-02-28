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
