<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade $folhadeatividade
 */
// pr($estagiario);
?>


<div class="areas form content">
    <aside>
        <div class="nav">
            <?= $this->Form->postLink(
                __('Excluir'),
                ['action' => 'delete', $folhadeatividade->id],
                ['confirm' => __('Tem certeza que quer excluir esta atividade # {0}?', $folhadeatividade->id), 'class' => 'button']
            ) ?>
            <?= $this->Html->link(__('Lista de atividades'), ['action' => 'index', $estagiario->estagiario->id], ['class' => 'button']) ?>
         
        </div>
    </aside>

    <div>
        <?= $this->Form->create($folhadeatividade) ?>
        <fieldset>
            <h3><?= __('Editando atividade') ?></h3>
            <?php
            echo $this->Form->control('estagiario_id', ['options' => [$estagiario->estagiario->id => $estagiario->estagiario->aluno->nome]]);
            echo $this->Form->control('dia');
            echo $this->Form->control('inicio');
            echo $this->Form->control('final');
            echo $this->Form->control('horario', ['type' => 'hidden', 'empty' => true]);
            echo $this->Form->control('atividade');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Enviar'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>