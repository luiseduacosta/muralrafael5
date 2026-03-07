<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visita $visita
 */
?>

<div>
    <div class="column-responsive column-80">
        <div class="visitas form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Visitas'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($visita) ?>
            <fieldset>
                <h3><?= __('Adicionar Visita') ?></h3>
                <?php
                    echo $this->Form->control('instituicao_id', ['default' => $instituicao_id ?? null, 'options' => $instituicoes, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('data', ['label' => 'Data']);
                    echo $this->Form->control('motivo', ['label' => 'Motivo']);
                    echo $this->Form->control('responsavel', ['label' => 'Responsável']);
                    echo $this->Form->control('descricao', ['label' => 'Descrição']);
                    echo $this->Form->control('avaliacao', ['label' => 'Avaliação']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
