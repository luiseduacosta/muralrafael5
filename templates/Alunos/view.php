<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Aluno $aluno
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="alunos view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(__('Excluir Aluno(a)'), ['action' => 'delete', $aluno->id], ['confirm' => __('Are you sure you want to delete {0}?', $aluno->nome), 'class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Novo Aluno(a)'), ['action' => 'add'], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Listar Alunos(as)'), ['action' => 'index'], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Editar Aluno(a)'), ['action' => 'edit', $aluno->id], ['class' => 'button' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Declaração de período'), ['controller' => 'Alunos', 'action' => 'declaracaoperiodo', $aluno->id], ['class' => 'button' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Termo de compromisso'), ['controller' => 'Estagiarios', 'action' => 'termocompromisso', '?' => ['aluno_id' => $aluno->id]], ['class' => 'button' , 'style' => 'width: 20%;']) ?>
                    <?php elseif ($user_data['aluno_id'] && ($user_data['aluno_id'] == $aluno->id)) : ?>
                        <?= $this->Html->link(__('Editar Aluno(a)'), ['action' => 'edit', $aluno->id], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Declaração de período'), ['controller' => 'Alunos', 'action' => 'declaracaoperiodo', $aluno->id], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Termo de compromisso'), ['controller' => 'Estagiarios', 'action' => 'termocompromisso', '?' => ['aluno_id' => $aluno->id]], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3><?= h($aluno->nome) ?></h3>
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
                    <th><?= __('Nome Social') ?></th>
                    <td><?= h($aluno->nomesocial) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data de Nascimento') ?></th>
                    <td><?= h($aluno->nascimento ? $aluno->nascimento->format('d/m/Y') : '') ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro') ?></th>
                    <td><?= h($aluno->registro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ingresso') ?></th>
                    <td><?= h($aluno->ingresso ?? 's/d') ?></td>
                </tr>
                <tr>
                    <th><?= __('Turno') ?></th>
                    <td><?= h($aluno->Turno->turno ?? 's/d') ?></td>
                </tr>
                <tr>
                    <th><?= __('CPF') ?></th>
                    <td><?= h($aluno->cpf) ?></td>
                </tr>
                <tr>
                    <th><?= __('Identidade') ?></th>
                    <td><?= h($aluno->identidade ?? 's/d') ?></td>
                </tr>
                <tr>
                    <th><?= __('Orgão expedidor') ?></th>
                    <td><?= h($aluno->orgao) ?></td>
                </tr>
                <tr>
                    <th><?= __('E-mail') ?></th>
                    <td><?= h($aluno->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <?php if (!empty((string)$aluno->telefone) && strlen((string)$aluno->telefone) < 10) : ?>
                    <td><?= '(' . h($aluno->codigo_telefone) . ') ' . h($aluno->telefone) ?></td>
                    <?php else : ?>
                    <td><?= h($aluno->telefone ?? 's/d') ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th><?= __('Celular') ?></th>
                    <?php if (!empty((string)$aluno->celular) && strlen((string)$aluno->celular) < 10) : ?>
                    <td><?= '(' . h($aluno->codigo_celular) . ') ' . h($aluno->celular) ?></td>
                    <?php else : ?>
                    <td><?= h($aluno->celular) ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th><?= __('Endereço') ?></th>
                    <td><?= h($aluno->endereco ?? 's/d' . ' - ' . $aluno->bairro ?? 's/d' . ' - ' . $aluno->municipio ?? 's/d' . ' - ' . $aluno->cep ?? 's/d') ?></td>
                </tr>
            </table>
            <?php if (!empty($aluno->observacoes)) : ?>
            <div class="text">
                <strong><?= __('Observacoes') ?></strong>
                <blockquote>
                    <?= $this->Markdown->parse($aluno->observacoes); ?>
                </blockquote>
            </div>
            <?php endif; ?>
            
            <?php if ($user_data['administrador_id']) : ?>
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
                                    <?php if ($user_data['administrador_id']) : ?>
                                        <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Users', 'action' => 'delete', $aluno->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $aluno->user->id)]) ?>
                                    <?php endif; ?>
                                </td>   
                                <td><?= $this->Html->link((string)$aluno->user->id, ['controller' => 'Users', 'action' => 'view', $aluno->user->id]) ?></td>
                                <td><?= $aluno->user->email ? $this->Text->autoLinkEmails($aluno->user->email) : '' ?></td>
                                <td><?= h($aluno->user->created ? $aluno->user->created->format('d/m/Y H:i:s') : 's/d') ?></td>
                                <td><?= h($aluno->user->modified ? $aluno->user->modified->format('d/m/Y H:i:s') : 's/d') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
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
                                <?php if ($user_data['administrador_id']) : ?>
                                    <?= $this->Html->link(__('Editar'), ['controller' => 'Inscricoes', 'action' => 'edit', $inscricao->id]) ?>
                                    <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Inscricoes', 'action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $inscricao->id)]) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $this->Html->link(h((string)$inscricao->id), ['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]) ?></td>
                            <td><?= $inscricao->muralestagio->instituicao_entidade ? $this->Html->link($inscricao->muralestagio->instituicao_entidade->instituicao, ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : $inscricao->muralestagio_id ?></td>
                            <td><?= h($inscricao->data ? $inscricao->data->format('d/m/Y') : '') ?></td>
                            <td><?= h($inscricao->periodo) ?></td>
                            <td><?= h($inscricao->timestamp ? $inscricao->timestamp->format('d/m/Y H:i:s') : '') ?></td>
                        </tr>
                        <?php endforeach; ?>
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
                            <th><?= __('Supervisor(a)') ?></th>
                            <th><?= __('Professor(a)') ?></th>
                            <th><?= __('Nivel') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('CH') ?></th>
                        </tr>
                        <?php foreach ($aluno->estagiarios as $estagiario) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?>
                                <?php if ($user_data['administrador_id']) : ?>
                                    <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Estagiarios', 'action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete {0}?', $estagiario->id)]) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $this->Html->link((string)$estagiario->id, ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?></td>
                            <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                            <td><?= h($estagiario->periodo) ?></td>
                            <td><?= h($estagiario->aluno->turno->turno ?? 's/d') ?></td>
                            <td><?= ($estagiario->supervisor and $estagiario->supervisor->nome) ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                            <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                            <td><?= h($estagiario->nivel) ?></td>
                            <td><?= h($estagiario->nota) ?></td>
                            <td><?= h($estagiario->ch) ?></td>
                            <td><?= h($estagiario->observacoes) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
      
        </div>
    </div>
</div>