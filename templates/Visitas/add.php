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
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'class' => 'form-control']);
                    echo $this->Form->control('data');
                    echo $this->Form->control('motivo');
                    echo $this->Form->control('responsavel');
                    echo $this->Form->control('descricao');
                    echo $this->Form->control('avaliacao');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
