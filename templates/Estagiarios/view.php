<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */

declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="estagiarios view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
					
			        <?php if ($user_data['administrador_id']): ?>
	                    <?= $this->Html->link(__('Editar Estagiario'), ['action' => 'edit', $estagiario->id], ['class' => 'button']) ?>
	                    <?= $this->Form->postLink(__('Deletar Estagiario'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id), 'class' => 'button']) ?>
	                    <?= $this->Html->link(__('Novo Estagiario'), ['action' => 'add'], ['class' => 'button']) ?>
					<?php endif; ?>
                </div>
            </aside>
            <h3>estagiario_<?= h($estagiario->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($estagiario->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Aluno') ?></th>
                    <td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituicao') ?></th>
                    <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Periodo') ?></th>
                    <td><?= h($estagiario->periodo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Turno') ?></th>
                    <td><?= $estagiario->turno ? h($estagiario->turno->turno) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Supervisor') ?></th>
                    <td><?= $estagiario->supervisor ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Professor') ?></th>
                    <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Turma') ?></th>
                    <td><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></td>
                </tr>
				<tr>
                    <th><?= __('Complemento') ?></th>
                    <td>
						<?= $estagiario->complemento ? h($estagiario->complemento->periodo_especial) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Nivel') ?></th>
                    <td><?= h($estagiario->nivel) ?></td>
                </tr>
                <tr>
                    <th><?= __('Observacoes') ?></th>
                    <td><?= h($estagiario->observacoes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro') ?></th>
                    <td><?= h($estagiario->registro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tc') ?></th>
                    <td><?= $this->Number->format($estagiario->tc ?? '') ?></td>
                </tr>
                <tr>
                    <th><?= __('Nota') ?></th>
                    <td><?= $this->Number->format($estagiario->nota ?? '') ?></td>
                </tr>
                <tr>
                    <th><?= __('Ch') ?></th>
                    <td><?= $this->Number->format($estagiario->ch ?? '') ?></td>
                </tr>
                <tr>
                    <th><?= __('Ajuste curricular') ?></th>
                    <td><?= h($estagiario->ajustecurricular2020) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tc Solicitacao') ?></th>
                    <td><?= h($estagiario->tc_solicitacao) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
