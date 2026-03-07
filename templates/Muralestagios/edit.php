<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muralestagio $muralestagio
 */
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="muralestagios form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Mural estagios'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['categoria'] == '1'): ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $muralestagio->id],
                            ['confirm' => __('Are you sure you want to delete # {0}?', $muralestagio->id), 'class' => 'button']
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($muralestagio) ?>
            <fieldset>
                <h3><?= __('Editando estagio_' . $muralestagio->instituicao) ?></h3>
                <?php
                    echo $this->Form->control('instituicao_id', ['readonly' => true, 'class' => 'form-control']);
                    echo $this->Form->control('email');
                    echo $this->Form->control('convenio', ['options' => ['1' => 'Sim', '0' => 'Não'], 'class' => 'form-control']);
                    echo $this->Form->control('vagas');
                    echo $this->Form->control('periodo', ['label' => 'Período']);
                    echo $this->Form->control('beneficios', ['label' => 'Benefícios: bolsa, alimentação, transporte, etc.']);
                    echo $this->Form->control('fim_de_semana', ['options' => ['1' => 'Sim', '0' => 'Não', '2' => 'Parcialmente'], 'class' => 'form-control']);
                    echo $this->Form->control('cargaHoraria', ['label' => 'Carga horária', 'placeholder' => 'Ex: 15']);
                    echo $this->Form->control('requisitos', ['class' => 'formCode hidden']);
                    echo $this->element('input_div', ['name' => 'requisitos', 'content' => $muralestagio->requisitos ]);
                    // echo $this->Form->control('turma_id', ['options' => $turmas, 'class' => 'form-control']);
                    // echo $this->Form->control('turno_id', ['options' => $turnos, 'class' => 'form-control']);
                    // echo $this->Form->control('professor_id', ['options' => $professores, 'class' => 'form-control']);
                    echo $this->Form->control('localInscricao', ['label' => 'Local de inscrição', 'options' => [1 => 'Inscrição somente no mural da Coordenação de Estágio da ESS', 0 => 'Inscrição na Instituição e no mural da Coordenação de Estágio da ESS']]);
                    echo $this->Form->control('dataInscricao', ['type' => 'date', 'empty' => true, 'label' => 'Data de enecerramento da inscrição']);
                    echo $this->Form->control('localSelecao', ['label' => 'Local de seleção', 'placeholder' => 'Será informado oportunamente']);
                    echo $this->Form->control('dataSelecao', ['type' => 'date', 'empty' => true, 'label' => 'Data de seleção']);
                    echo $this->Form->control('horarioSelecao', ['type' => 'time', 'empty' => true, 'placeholder' => '9:00', 'class' => 'form-control']);
                    echo $this->Form->control('formaSelecao', ['label' => 'Forma de seleção', 'options' => [0 => 'Entrevista', 2 => 'Prova', 1 => 'CR', 3 => 'Outra']]);
                    echo $this->Form->control('contato', ['label' => 'Contato']);
                    echo $this->Form->control('outras', ['class' => 'formCode hidden']);
                    echo $this->element('input_div', ['name' => 'outras', 'content' => $muralestagio->outras ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
