<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\InstSuper $instSuper
 */
?>

<div class="container">

        <?= $this->Html->link(__('Listar Instituição-Supervisor'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2', 'style' => 'font-size: 10pt;']) ?>
        <div class="card">
            <div class="card-header">
                <h4><?= __('Editar Instituição-Supervisor') ?></h4>
            </div>
        <div class="card-body">
            <?= $this->Form->create($instSuper) ?>
            <fieldset>
                <?php
                echo $this->Form->control('instituicao_id', [
                    'label' => 'Instituição',
                    'options' => $instituicoes,
                    'empty' => true,
                    'class' => 'form-control',
                    'required' => true,
                ]);
                echo $this->Form->control('supervisor_id', [
                    'label' => 'Supervisor',
                    'options' => $supervisores,
                    'empty' => true,
                    'class' => 'form-control',
                    'required' => true,
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'btn btn-success']) ?>
            <?= $this->Form->end() ?>
        </div>
</div>
