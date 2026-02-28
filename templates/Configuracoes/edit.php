<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Configuracao $configuracao
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="configuracao form content">
            <?= $this->Form->create($configuracao) ?>
            <fieldset>
                <h3><?= __('Editando Configurações') ?></h3>
                <?php
                    echo $this->Form->control('instituicao');
                    echo $this->Form->control('descricao');
                    echo $this->Form->control('mural_periodo_atual');
                    echo $this->Form->control('curso_turma_atual');
                    echo $this->Form->control('curso_abertura_inscricoes');
                    echo $this->Form->control('curso_encerramento_inscricoes');
                    echo $this->Form->control('termo_compromisso_periodo');
                    echo $this->Form->control('termo_compromisso_inicio');
                    echo $this->Form->control('termo_compromisso_final');
                    echo $this->Form->control('periodo_calendario_academico');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
