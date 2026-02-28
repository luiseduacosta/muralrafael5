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
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $muralestagio->id],
                        ['confirm' => __('Are you sure you want to delete # {0}?', $muralestagio->id), 'class' => 'button']
                    ) ?>
                </div>
            </aside>
            <?= $this->Form->create($muralestagio) ?>
            <fieldset>
                <h3><?= __('Editando estagio_' . $muralestagio->id) ?></h3>
                <?php
                    echo $this->Form->control('instituicao');
                    echo $this->Form->control('convenio');
                    echo $this->Form->control('email');
                    echo $this->Form->control('vagas');
                    echo $this->Form->control('beneficios');
                    echo $this->Form->control('fim_de_semana');
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
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
