<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turno $turno
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="turnos form content">
            <aside>
                <div class="nav">
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $turno->id],
                        ['confirm' => __('Are you sure you want to delete # {0}?', $turno->id), 'class' => 'button']
                    ) ?>
                    <?= $this->Html->link(__('Listar Turnos'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($turno) ?>
            <fieldset>
                <h3><?= __('Editando turno_') . $turno->id ?></h3>
                <?php
                    echo $this->Form->control('turno');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
