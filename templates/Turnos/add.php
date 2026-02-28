<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turno $turno
 */
?>
<div>
    <div class="column column-80">
        <div class="turnos form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Turnos'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($turno) ?>
            <fieldset>
                <h3><?= __('Adicionar Turno') ?></h3>
                <?php
                    echo $this->Form->control('turno');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
