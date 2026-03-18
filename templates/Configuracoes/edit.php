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
                    echo $this->Form->control('instituicao_curso', ['label' => 'Instiuiçõa - Curso']);
                    echo $this->Form->control('mural_periodo_atual', ['label' => 'Período Atual Mural']);
                    echo $this->Form->control('termo_compromisso_periodo', ['label' => 'Período Termo Compromisso']);
                    echo $this->Form->control('termo_compromisso_inicio', ['label' => 'Inicio Termo Compromisso']);
                    echo $this->Form->control('termo_compromisso_final', ['label' => 'Final Termo Compromisso']);
                    echo $this->Form->control('periodo_calendario_academico', ['label' => 'Período Calendário Acadêmico']);
                    echo $this->Form->control('curso_turma_atual', ['label' => 'Curso Turma Atual']);
                    echo $this->Form->control('curso_abertura_inscricoes', ['label' => 'Curso Abertura Inscrições']);
                    echo $this->Form->control('curso_encerramento_inscricoes', ['label' => 'Curso Encerramento Inscrições']);

                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
