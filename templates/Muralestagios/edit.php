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
                <h3><?= __('Editando estagio_' . $muralestagio->id) ?></h3>
                <?php
                    echo $this->Form->control('instituicao');
                    echo $this->Form->control('convenio', ['options' => ['1' => 'Sim', '0' => 'Não'], 'class' => 'form-control']);
                    echo $this->Form->control('email');
                    echo $this->Form->control('vagas');
                    echo $this->Form->control('beneficios');
                    echo $this->Form->control('fim_de_semana', ['options' => ['1' => 'Sim', '0' => 'Não', '2' => 'Parcialmente'], 'class' => 'form-control']);
                    echo $this->Form->control('carga_horaria');
                    echo $this->Form->control('requisitos', ['class' => 'formCode hidden']);
                    echo $this->element('input_div', ['name' => 'requisitos', 'content' => $muralestagio->requisitos ]);
                    echo $this->Form->control('turma_id', ['options' => $turmas, 'class' => 'form-control']);
                    echo $this->Form->control('turno_id', ['options' => $turnos, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'class' => 'form-control']);
                    echo $this->Form->control('local_inscricao');
                    echo $this->Form->control('data_inscricao', ['empty' => true]);
                    echo $this->Form->control('local_selecao');
                    echo $this->Form->control('data_selecao', ['empty' => true]);
                    echo $this->Form->control('horario_selecao');
                    echo $this->Form->control('forma_selecao');
                    echo $this->Form->control('contato');
                    echo $this->Form->control('periodo');
                    echo $this->Form->control('outras', ['class' => 'formCode hidden']);
                    echo $this->element('input_div', ['name' => 'outras', 'content' => $muralestagio->outras ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
