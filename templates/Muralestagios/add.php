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
                    echo $this->Form->control('convenio', ['label' => 'Convênio', 'type' => 'select', 'options' => [1 => 'Sim', 0 => 'Não'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('vagas', ['label' => 'Número de vagas', 'default' => '1', 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['value' => $periodo, 'class' => 'form-control']);
                    echo $this->Form->control('beneficios', ['placeholder' => 'Bolsa, alimentação, transporte, etc.', 'label' => 'Benefícios', 'class' => 'form-control']);
                    echo $this->Form->control('fim_de_semana', ['label' => 'Fim de semana', 'type' => 'select', 'options' => [2 => 'Parcialmente', 1 => 'Sim', 0 => 'Não'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('carga_horaria', ['placeholder' => '12', 'label' => 'Carga horária', 'class' => 'form-control']);
                    echo $this->Form->control('requisitos', ['placeholder' => 'Ética aprovada', 'label' => 'Requisitos', 'class' => 'form-control']);
                    echo $this->Form->control('local_inscricao', ['label' => 'Local de inscrição', 'type' => 'select', 'options' => [1 => 'Inscrição somente no mural da Coordenação de Estágio da ESS', 0 => 'Inscrição na Instituição e no mural da Coordenação de Estágio da ESS'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('data_inscricao', ['label' => 'Data de encerramento das inscrições', 'type' => 'date', 'empty' => true]);
                    echo $this->Form->control('local_selecao', ['label' => 'Local de seleção', 'type' => 'select', 'options' => [1 => 'Local na Coordenação de Estágio da ESS', 0 => 'Local na Instituição'], 'empty' => false, 'default' => '0', 'class' => 'form-control']);
                    echo $this->Form->control('data_selecao', ['label' => 'Data de seleção', 'type' => 'date', 'empty' => true]);
                    echo $this->Form->control('horario_selecao', ['label' => 'Horário de seleção', 'type' => 'time', 'empty' => true, 'placeholder' => '9:00', 'class' => 'form-control']);
                    echo $this->Form->control('forma_selecao', ['label' => 'Forma de seleção', 'type' => 'select', 'options' => [0 => 'Entrevista', 2 => 'Prova', 1 => 'CR', 3 => 'Outra'], 'empty' => false, 'default' => '0']);
                    echo $this->Form->control('contato', ['label' => 'Informações de contato']);
                    echo $this->Form->control('outras', ['label' => 'Outras informações']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
