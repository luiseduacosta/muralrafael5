<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */
declare(strict_types=1);

use Cake\Utility\Inflector;
use Cake\I18n\Date;

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
                    echo $this->Form->control('registro', ['required' => true]);
                    echo $this->Form->control('turno', ['options' => ['d' => 'Diurno', 'n' => 'Noturno', 'i' => 'Indeterminado'], 'value' => $aluno->turno ?? '', 'readonly']);
                    echo $this->Form->control('nivel', ['required' => true]);
                    echo $this->Form->control('ajuste2020', ['options' => ['1' => 'Sim (3 semestres)', '0' => 'Nao (4 semestres)']]);
                    echo $this->Form->control('tc', ['label' => 'Termo de compromisso assinado S/N?', 'options' => ['1' => 'Sim', '0' => 'Nao']]);
                    echo $this->Form->control('tc_solicitacao', ['label' => 'Data de solicitacao do termo de compromisso', 'required' => false]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'required' => true, 'empty'=> true, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'required' => true, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'required' => false, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['label' => 'Periodo', 'required' => true]);
                    echo $this->Form->control('complemento_id', ['options' => $complementos, 'empty' => true, 'required' => false]);
                    echo $this->Form->control('turma_id', ['options' => $turmas, 'required' => false, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('benetransporte', ['label' => 'Beneficio de transporte', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'required' => false]);
                    echo $this->Form->control('benealimentacao', ['label' => 'Beneficio de alimentacao', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'required' => false]);
                    echo $this->Form->control('benbolsa', ['label' => 'Beneficio de bolsa - valor em R$', 'required' => false, 'type' => 'text']);
                    if ($user_data['categoria'] == "1" || $user_data['categoria'] == "3" && ($user_data['professor_id'] == $estagiario->professor_id)) {
                        echo $this->Form->control('nota', ['label' => 'Nota', 'required' => false]);
                        echo $this->Form->control('ch', ['label' => 'Carga horária', 'required' => false]);
                    }
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
