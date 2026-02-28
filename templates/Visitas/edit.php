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
                <?= $this->Form->postLink(
                    __('Deletar'),
                    ['action' => 'delete', $visita->id],
                    ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id), 'class' => 'button']
                ) ?>
            </div>
        </aside>
            <?= $this->Form->create($visita) ?>
            <fieldset>
                <h3><?= __('Editando visita_') . $visita->id ?></h3>
                <?php
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes]);
                    echo $this->Form->control('data');
                    echo $this->Form->control('motivo');
                    echo $this->Form->control('responsavel');
                    echo $this->Form->control('descricao');
                    echo $this->Form->control('avaliacao');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
