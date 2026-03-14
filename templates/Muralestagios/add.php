<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muralestagio $muralestagio
 */
?>

<div>
    <div class="column-responsive column-80">
        <div class="muralestagios form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Mural estagios'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($muralestagio) ?>
            <fieldset>
                <h3><?= __('Adicionar vagas de estagio') ?></h3>
                <?php
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('email', ['label' => 'E-mail']);
                    echo $this->Form->control('convenio', ['type' => 'select', 'options' => [1 => 'Sim', 0 => 'Não'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('vagas', ['label' => 'Número de vagas', 'default' => '1']);
                    echo $this->Form->control('periodo', ['value' => $periodo]);
                    echo $this->Form->control('beneficios', ['placeholder' => 'Bolsa, alimentação, transporte, etc.']);
                    echo $this->Form->control('fim_de_semana', ['type' => 'select', 'options' => [2 => 'Parcialmente', 1 => 'Sim', 0 => 'Não'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('cargaHoraria', ['placeholder' => '12']);
                    echo $this->Form->control('requisitos', ['placeholder' => 'Ética aprovada']);
                    // echo $this->Form->control('turma', ['options' => $turmas, 'label' => 'Turma', 'empty' => true, 'class' => 'form-control']);
                    // echo $this->Form->control('turno_id', ['type' => 'select', 'options' => $turnos, 'empty' => true, 'class' => 'form-control']);
                    // echo $this->Form->control('professor_id', ['options' => $professores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('localInscricao', ['type' => 'select', 'options' => [1 => 'Inscrição somente no mural da Coordenação de Estágio da ESS', 0 => 'Inscrição na Instituição e no mural da Coordenação de Estágio da ESS'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('dataInscricao', ['type' => 'date', 'empty' => true, 'label' => 'Data de encerramento das inscrições']);
                    echo $this->Form->control('localSelecao', ['placeholder' => 'Fique atento(a). Será informado oportunamente.', 'label' => 'Local de seleção']);
                    echo $this->Form->control('dataSelecao', ['type' => 'date', 'empty' => true, 'label' => 'Data de seleção']);
                    echo $this->Form->control('horarioSelecao', ['type' => 'time', 'empty' => true, 'placeholder' => '9:00', 'class' => 'form-control']);
                    echo $this->Form->control('formaSelecao',  ['type' => 'select', 'options' => [0 => 'Entrevista', 2 => 'Prova', 1 => 'CR', 3 => 'Outra'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('contato', ['label' => 'Informações de contato']);
                    echo $this->Form->control('outras', ['label' => 'Outras informações']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
