<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade $folhadeatividade
 * @var \App\Model\Entity\Estagiario|null $estagiario
 */
declare(strict_types=1);
?>

<div class="folhadeatividades edit content">
    <aside>
        <div class="nav">
            <?= $this->Form->postLink(
                __('Excluir'),
                ['action' => 'delete', $folhadeatividade->id],
                ['confirm' => __('Tem certeza que quer excluir esta atividade # {0}?', $folhadeatividade->id), 'class' => 'button'],
            ) ?>
            <?php if (isset($estagiario)): ?>
                <?= $this->Html->link(__('Lista de atividades'), ['action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php else: ?>
                <?= $this->Html->link(__('Lista de atividades'), ['action' => 'index', '?' => ['estagiario_id' => $folhadeatividade->estagiario_id]], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>

    <div>
        <?= $this->Form->create($folhadeatividade) ?>
        <fieldset>
            <h3><?= __('Editando atividade') ?></h3>
            <?php
            if (isset($estagiario->aluno->nome)) {
                echo $this->Form->control('estagiario_id', ['options' => [$estagiario->id => $estagiario->aluno->nome], 'readonly' => true]);
            } else {
                echo $this->Form->control('estagiario_id', ['type' => 'number', 'readonly' => true]);
            }
            echo $this->Form->control('dia', ['type' => 'date', 'label' => 'Data']);
            echo $this->Form->control('inicio', ['type' => 'time', 'label' => 'Horário de início']);
            echo $this->Form->control('final', ['type' => 'time', 'label' => 'Horário de finalização']);
            echo $this->Form->control('horario', ['type' => 'hidden']);
            echo $this->Form->control('atividade', ['type' => 'text', 'label' => 'Atividade realizada']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Enviar'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>