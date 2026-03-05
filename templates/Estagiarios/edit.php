<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<div>
    <div class="column-responsive column-80">
        <div class="estagiarios form content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']): ?>
                        <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php elseif ($user_data['aluno_id'] == $estagiario->aluno_id): ?>
                        <?= $this->Html->link(__('Ver aluno'), ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno_id], ['class' => 'button']) ?>
                    <?php endif; ?>
                    <?php if ($user_data['categoria'] == "1"): ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $estagiario->id],
                            ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id), 'class' => 'button']
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($estagiario) ?>
            <fieldset>
                <h3><?= __('Editando estagiario_' . $estagiario->id) ?></h3>
                <?php
                    echo $this->Form->control('aluno_id', ['options' => $alunos, 'class' => 'form-control']);
                    echo $this->Form->control('registro');
                    echo $this->Form->control('ajustecurricular2020');
                    echo $this->Form->control('turno_id', ['options' => $turnos, 'class' => 'form-control']);
                    echo $this->Form->control('nivel');
                    echo $this->Form->control('tc');
                    echo $this->Form->control('tc_solicitacao', ['empty' => true]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo');
                    echo $this->Form->control('turma_id', ['options' => $turmas, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('nota');
                    echo $this->Form->control('ch');
                    echo $this->Form->control('observacoes');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
