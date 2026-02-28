<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Area $area
 */
?>
<div class="areas form content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Listar Areas'), ['action' => 'index'], ['class' => 'button']) ?>
        </div>
    </aside>
    <?= $this->Form->create($area) ?>
    <fieldset>
        <h3><?= __('Adicionando Area') ?></h3>
        <?php
            echo $this->Form->control('area');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
    <?= $this->Form->end() ?>
</div>
