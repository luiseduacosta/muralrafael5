<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Complemento $periodo_especial
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="complementos form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Complementos'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($complemento) ?>
            <fieldset>
                <h3><?= __('Adicionando Complemento') ?></h3>
                <?php
                    echo $this->Form->control('periodo_especial');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
