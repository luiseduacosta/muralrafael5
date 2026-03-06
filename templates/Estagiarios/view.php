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
        <div class="estagiarios view content">
            <aside>
                <div class="nav">
			        <?php if ($user_data->categoria == '1'): ?>
                        <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
	                    <?= $this->Html->link(__('Editar Estagiario'), ['action' => 'edit', $estagiario->id], ['class' => 'button']) ?>
	                    <?= $this->Form->postLink(__('Excluir Estagiario'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id), 'class' => 'button']) ?>
	                    <?= $this->Html->link(__('Novo Estagiario'), ['action' => 'add'], ['class' => 'button']) ?>
					<?php endif; ?>
                    <?php if ($user_data->categoria == '2' && $user_data->aluno_id == $estagiario->aluno_id): ?>
                        <?= $this->Html->link(__('<- Aluno(a)'), ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Declaração de estágio'), ['action' => 'declaracaodeestagio', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Atividades'), ['controller' => 'Folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Avaliação'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3>Estagiario(a): <?= h($estagiario->aluno->nome) ?></h3>
            <dl>
                <div class="row">
                    <dt class="col-2"><?= __('Id') ?></dt>
                    <dd><?= h($estagiario->id) ?></dd>
                </div>
                
                <div class="row">
                    <dt class="col-2"><?= __('Aluno(a)') ?></dt>
                    <dd><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Registro') ?></dt>
                    <dd><?= h($estagiario->registro) ?></dd>
                </div>                
                <div class="row">
                    <dt class="col-2"><?= __('Instituicao') ?></dt>
                    <dd><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></dd>
                </div>                
                <div class="row">
                    <dt class="col-2"><?= __('Periodo') ?></dt>
                    <dd><?= h($estagiario->periodo) ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Nivel') ?></dt>
                    <dd><?= h($estagiario->nivel) ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Turno') ?></dt>
                    <dd><?= $estagiario->turno_entidade ? h($estagiario->turno_entidade->turno) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Ajuste curricular') ?></dt>
                    <dd><?= h($estagiario->ajuste2020) ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Tc Solicitacao') ?></dt>
                    <dd><?= h($estagiario->tc_solicitacao) ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Termo de Compromisso assinado') ?></dt>
                    <dd><?= $this->Number->format($estagiario->tc ?? '') ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Supervisor(a)') ?></dt>
                    <dd><?= $estagiario->supervisor ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Professor(a)') ?></dt>
                    <dd><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Turma') ?></dt>
                    <dd><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Complemento (Periodo Especial)') ?></dt>
                    <dd><?= $estagiario->complemento ? h($estagiario->complemento->periodo_especial) : '' ?></dd>
                </div>
                <div class="row">
                    <dt class="col-2"><?= __('Nota') ?></dt>
                    <dd><?= $this->Number->format($estagiario->nota ?? '') ?></dd>
                </div>                
                <div class="row">
                    <dt class="col-2"><?= __('CH') ?></dt>
                    <dd><?= $this->Number->format($estagiario->ch ?? '') ?></dd>
                </div>                
                <div class="row">
                    <dt class="col-2"><?= __('Observações') ?></dt>
                    <dd><?= h($estagiario->observacoes) ?></dd>
                </div>
            </dl>
        </div>
        <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
        <?= $this->Html->link('Editar', ['action' => 'edit', $estagiario->id], ['class' => 'button float-right']); ?>        
    </div>
</div>
