<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */
?>
<div">
    <div class="column-responsive column-80">
        <div class="estagiarios form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($estagiario) ?>
            <fieldset>
                <h3><?= __('Adicionando Estagiario') ?></h3>
                <?php
                    echo $this->Form->hidden('aluno_id', ['value' => $aluno->id]);
                    echo $this->Form->control('aluno_nome', ['label' => 'Aluno', 'value' => $aluno->nome, 'readonly' => true]);
                    echo $this->Form->control('registro', ['value' => $aluno->registro, 'readonly']);
                    echo $this->Form->control('ajuste2020');
                    echo $this->Form->control('turno', ['options' => ['diurno' => 'Diurno', 'noturno' => 'Noturno'], 'value' => $aluno->turno, 'readonly']);
                    echo $this->Form->control('nivel', ['value' => $estagiario->nivel ? $estagiario->nivel + 1 : 1]);
                    echo $this->Form->control('tc', ['required' => false, 'empty' => true, 'default' => null]);
                    echo $this->Form->control('tc_solicitacao', ['value' => \Cake\I18n\DateTime::now()->format('Y-m-d'), 'readonly' => true]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['value' => $periodo, 'readonly' => true]);
                    echo $this->Form->control('turma_id', ['options' => $turmas, 'empty' => true]);
                    echo $this->Form->control('nota');
                    echo $this->Form->control('ch');
                    echo $this->Form->control('observacoes');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
