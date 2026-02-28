<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Aluno $aluno
 */
declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="alunos view content">
            <aside>
                <div class="nav">
        	        <?php if ($user_data['administrador_id']): ?>
                        <?= $this->Html->link(__('Novo Aluno'), ['action' => 'add'], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Listar Alunos'), ['action' => 'index'], ['class' => 'button']) ?>
        	        <?php endif; ?>
                    <?= $this->Html->link(__('Editar Aluno'), ['action' => 'edit', $aluno->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Aluno'), ['action' => 'delete', $aluno->id], ['confirm' => __('Are you sure you want to delete {0}?', $aluno->nome), 'class' => 'button']) ?>
                </div>
            </aside>
            <h3>aluno_<?= h($aluno->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($aluno->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($aluno->nome) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro') ?></th>
                    <td><?= h($aluno->registro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ingresso') ?></th>
                    <td><?= h($aluno->ingresso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Cpf') ?></th>
                    <td><?= h($aluno->cpf) ?></td>
                </tr>
                <tr>
                    <th><?= __('Identidade') ?></th>
                    <td><?= h($aluno->identidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Orgao') ?></th>
                    <td><?= h($aluno->orgao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <td><?= '(' . h($aluno->codigo_telefone) . ') ' . h($aluno->telefone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Celular') ?></th>
                    <td><?= '(' . h($aluno->codigo_celular) . ') ' . h($aluno->celular) ?></td>
                </tr>
                <tr>
                    <th><?= __('Endereco') ?></th>
                    <td><?= h($aluno->endereco . ' - ' . $aluno->bairro . ' - ' . $aluno->municipio . ' - ' . $aluno->cep) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nascimento') ?></th>
                    <td><?= h($aluno->nascimento) ?></td>
                </tr>
            </table>
            <?php if (!empty($aluno->observacoes)) : ?>
            <div class="text">
                <strong><?= __('Observacoes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph($aluno->observacoes); ?>
                </blockquote>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($aluno->user)) : ?>
            <div class="related">
                <h4><?= __('Usuário') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Criado') ?></th>
                            <th><?= __('Modificado') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Users', 'action' => 'view', $aluno->user->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Users', 'action' => 'edit', $aluno->user->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Users', 'action' => 'delete', $aluno->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $aluno->user->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$aluno->user->id, ['controller' => 'Users', 'action' => 'view', $aluno->user->id]) ?></td>
                            <td><?= $aluno->user->email ? $this->Text->autoLinkEmails($aluno->user->email) : '' ?></td>
                            <td><?= h($aluno->user->created) ?></td>
                            <td><?= h($aluno->user->modified) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>


            <?php if (!empty($aluno->estagiarios)) : ?>
            <div class="related">
                <h4><?= __('Estágios') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Instituicao') ?></th>
                            <th><?= __('Periodo') ?></th>
                            <th><?= __('Turno') ?></th>
                            <th><?= __('Supervisor') ?></th>
                            <th><?= __('Professor') ?></th>
                            <th><?= __('Turma') ?></th>
                            <th><?= __('Nivel') ?></th>
                            <th><?= __('Tc') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('Ch') ?></th>
                        </tr>
                        <?php foreach ($aluno->estagiarios as $estagiario) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Estagiarios', 'action' => 'edit', $estagiario->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Estagiarios', 'action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete {0}?', $estagiario->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$estagiario->id, ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?></td>
                            <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                            <td><?= h($estagiario->periodo) ?></td>
                            <td>
        						<?php
        						$turno = '';
        						switch ( $estagiario->turno ) {
        							case 'D': $turno = 'Diurno';   break;
        							case 'N': $turno = 'Noturno';  break;
        							case 'A': $turno = 'Ambos';    break;
        		                    case 'I': $turno = 'Integral'; break;
        						}
        						echo h($turno);
        						?>
                            </td>
                            <td><?= ($estagiario->supervisor and $estagiario->supervisor->nome) ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                            <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                            <td><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></td>
                            <td><?= h($estagiario->nivel) ?></td>
                            <td><?= h($estagiario->tc) ?></td>
                            <td><?= h($estagiario->nota) ?></td>
                            <td><?= h($estagiario->ch) ?></td>
                            <td><?= h($estagiario->observacoes) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($aluno->inscricoes)) : ?>
            <div class="related">
                <h4><?= __('Inscrições') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Estagio') ?></th>
                            <th><?= __('Data') ?></th>
                            <th><?= __('Periodo') ?></th>
                            <th><?= __('Timestamp') ?></th>
                        </tr>
                        <?php foreach ($aluno->inscricoes as $inscricao) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Inscricoes', 'action' => 'edit', $inscricao->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Inscricoes', 'action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $inscricao->id)]) ?>
                            </td>
                            <td><?= $this->Html->link(h((string)$inscricao->id), ['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]) ?></td>
        					<td><?= $inscricao->muralestagio ? $this->Html->link($inscricao->muralestagio->instituicao ? $inscricao->muralestagio->instituicao->instituicao : $inscricao->muralestagio->id , ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : $inscricao->muralestagio_id ?></td>
                            <td><?= h($inscricao->data) ?></td>
                            <td><?= h($inscricao->periodo) ?></td>
                            <td><?= h($inscricao->timestamp) ? h($inscricao->timestamp) : '' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>