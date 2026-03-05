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
                    echo $this->Form->control('turno', ['options' => ['d' => 'Diurno', 'n' => 'Noturno', 'i' => 'Indeterminado'], 'value' => $aluno->turno, 'readonly']);
                    echo $this->Form->control('nivel', ['value' => $estagiario->nivel, 'readonly' => true, 'required' => true]);
                    echo $this->Form->control('tc', ['label' => 'Termo de compromisso assinado S/N', 'required' => false, 'default' => null]);
                    echo $this->Form->control('tc_solicitacao', ['label' => 'Data de Solicitação', 'value' => \Cake\I18n\DateTime::now()->format('Y-m-d'), 'readonly' => true]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['value' => $periodo, 'readonly' => true]);
                    echo $this->Form->control('nota', ['type' => 'hidden']); // Aluno can't fill this field
                    echo $this->Form->control('ch', ['type' => 'hidden']); // Aluno can't fill this field
                    echo $this->Form->control('complemento_id', ['type' => 'hidden', 'default' => null]); // Used only during the Covid-19
                    echo $this->Form->control('ajuste2020', ['options' => ['1' => 'Sim (3 semestres)', '0' => 'Nao (4 semestres)'], 'value' => $aluno->ajuste2020, 'readonly']);
                    echo $this->Form->control('benetransporte', ['label' => 'Transporte', 'required' => false, 'empty' => true, 'default' => null]);
                    echo $this->Form->control('benealimentacao', ['label' => 'Alimentação', 'required' => false, 'empty' => true, 'default' => null]);
                    echo $this->Form->control('benebolsa', ['label' => 'Valor do benefício de Bolsa']);
                    echo $this->Form->control('turno_id', ['options' => $turnos, 'empty' => true]);
                    echo $this->Form->control('turma_id', ['options' => $turmas, 'empty' => true]);
                    echo $this->Form->control('observacoes', ['label' => 'Observações']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
