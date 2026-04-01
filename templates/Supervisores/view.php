<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supervisor $supervisor
 */
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="supervisores view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Listar Supervisores'), ['action' => 'index'], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Editar Supervisor(a)'), ['action' => 'edit', $supervisor->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Supervisor(a)'), ['action' => 'delete', $supervisor->id], ['confirm' => __('Are you sure you want to delete {0}?', $supervisor->nome), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Novo(a) Supervisor(a)'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?php endif; ?>
                    <?php if ($user_data['supervisor_id'] && $user_data['supervisor_id'] == $supervisor->id) : ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $supervisor->id], ['class' => 'button']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3>supervisor_<?= h($supervisor->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($supervisor->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($supervisor->nome) ?></td>
                </tr>
                <tr>
                    <th><?= __('CPF') ?></th>
                    <td><?= h($supervisor->cpf) ?></td>
                </tr>
                <tr>
                    <th><?= __('CRESS') ?></th>
                    <td><?= (string)$supervisor->cress ?></td>
                </tr>
                <tr>
                    <th><?= __('Região') ?></th>
                    <td><?= $this->Number->format($supervisor->regiao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Codigo Tel') ?></th>
                    <td><?= h($supervisor->codigo_tel) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <?php if ($supervisor->telefone && (strlen($supervisor->telefone) < 8)) : ?>
                        <td><?= '(' . $supervisor->codigo_tel . ') ' . $supervisor->telefone ?></td>
                    <?php else : ?>
                        <td><?= $supervisor->telefone ?>
                    <?php endif ?>    
                </tr>
                <tr>
                    <th><?= __('Codigo Cel') ?></th>
                    <td><?= h($supervisor->codigo_cel) ?></td>
                </tr>
                <tr>
                    <th><?= __('Celular') ?></th>
                    <?php if ($supervisor->celular && (strlen($supervisor->celular) < 8)) : ?>
                            <td><?= '(' . $supervisor->codigo_cel . ') ' . $supervisor->celular ?></td>
                    <?php else : ?>        
                        <td><?= h($supervisor->celular) ?></td>
                    <?php endif ?>    
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= $supervisor->email ? $this->Text->autoLinkEmails($supervisor->email) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Cargo na instituição') ?></th>
                    <td><?= h($supervisor->cargo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Escola') ?></th>
                    <td><?= h($supervisor->escola) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano Formatura') ?></th>
                    <td><?= h($supervisor->ano_formatura) ?></td>
                </tr>
                <tr>
                    <th><?= __('Outros Estudos') ?></th>
                    <td><?= h($supervisor->outros_estudos) ?></td>
                </tr>
                <tr>
                    <th><?= __('Área do outro estudo') ?></th>
                    <td><?= h($supervisor->area_curso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de conclusão') ?></th>
                    <td><?= h($supervisor->ano_curso) ?></td>
                </tr>
                <tr>
                    <th><?= __('CEP') ?></th>
                    <td><?= h($supervisor->cep) ?></td>
                </tr>
                <tr>
                    <th><?= __('Endereço') ?></th>
                    <td><?= h($supervisor->endereco) ?></td>
                </tr>
                <tr>
                    <th><?= __('Bairro') ?></th>
                    <td><?= h($supervisor->bairro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Município') ?></th>
                    <td><?= h($supervisor->municipio) ?></td>
                </tr>
                <tr>
                    <th><?= __('Turma do curso de supervisores') ?></th>
                    <td><?= h($supervisor->curso_turma) ?></td>
                </tr>
                <tr>
                    <th><?= __('Num de inscrição no curso de supervisores') ?></th>
                    <td><?= $supervisor->num_inscricao ? $this->Number->format($supervisor->num_inscricao) : '' ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Observações') ?></strong>
                <blockquote>
                    <?= $this->Markdown->parse($supervisor->observacoes); ?>
                </blockquote>
            </div>
            
            <?php if (!empty($supervisor->user)) : ?>
                <?php if ($user_data['administrador_id'] || $user_data['supervisor_id'] == $supervisor->user->id) : ?>
                    <div class="related">
                        <h4><?= __('User') ?></h4>
                        <div class="table_wrap">
                            <table>
                                <tr>
                                    <th class="actions"><?= __('Actions') ?></th>
                                    <th><?= __('Id') ?></th>
                                    <th><?= __('Email') ?></th>
                                    <th><?= __('Registro') ?></th>
                                    <th><?= __('Timestamp') ?></th>
                                </tr>
                                <tr>
                                    <td class="actions">
                                        <?= $this->Html->link(__('Ver'), ['controller' => 'Users', 'action' => 'view', $supervisor->user->id]) ?>
                                        <?= $this->Html->link(__('Editar'), ['controller' => 'Users', 'action' => 'edit', $supervisor->user->id]) ?>
                                        <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Users', 'action' => 'delete', $supervisor->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $supervisor->user->id)]) ?>
                                    </td>
                                    <td><?= h($supervisor->user->id) ?></td>
                                    <td><?= $supervisor->user->email ? $this->Text->autoLinkEmails($supervisor->user->email) : '' ?></td>
                                    <td><?= h($supervisor->user->registro) ?></td>
                                    <td><?= $supervisor->user->timestamp ? h($supervisor->user->timestamp) : '' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (!empty($supervisor->instituicoes)) : ?>
            <div class="related">
                <h4><?= __('Instituições') ?></h4>
                    <div class="table_wrap">
                        <table>
                            <tr>
                                <th class="actions"><?= __('Actions') ?></th>
                                <th><?= __('Id') ?></th>
                                <th><?= __('Instituição') ?></th>
                                <th><?= __('Área') ?></th>
                                <th><?= __('Natureza') ?></th>
                                <th><?= __('CNPJ') ?></th>
                                <th><?= __('Email') ?></th>
                                <th><?= __('URL') ?></th>
                                <th><?= __('Convênio') ?></th>
                            </tr>
                            <?php foreach ($supervisor->instituicoes as $instituicao) : ?>
                                <tr>
                                    <td class="actions">
                                        <?= $this->Html->link(__('Ver'), ['controller' => 'Instituicoes', 'action' => 'view', $instituicao->id]) ?>
                                        <?php if ($user_data['administrador_id']) : ?>
                                            <?= $this->Html->link(__('Editar'), ['controller' => 'Instituicoes', 'action' => 'edit', $instituicao->id]) ?>
                                            <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Instituicoes', 'action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete {0}?', $instituicao->instituicao)]) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= h($instituicao->id) ?></td>
                                    <td><?= $this->Html->link($instituicao->instituicao, ['controller' => 'instituicoes', 'action' => 'view', $instituicao->id]) ?></td>
                                    <td><?= $instituicao->area ? $this->Html->link(h($instituicao->area->area), ['controller' => 'Areas', 'action' => 'view', $instituicao->area->id]) : '' ?></td>
                                    <td><?= h($instituicao->natureza) ?></td>
                                    <td><?= h($instituicao->cnpj) ?></td>
                                    <td><?= $this->Text->autoLinkEmails($instituicao->email) ?></td>
                                    <td><?= $instituicao->url ? $this->Html->link($instituicao->url) : '' ?></td>
                                    <td><?= h($instituicao->convenio) == '1' ? __('Sim') : __('Não') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($supervisor->estagiarios)) : ?>
            <div class="related">
                <h4><?= __('Estagiarios') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Aluno') ?></th>
                            <th><?= __('Registro') ?></th>
                            <th><?= __('Turno') ?></th>
                            <th><?= __('Nivel') ?></th>
                            <th><?= __('TC assinado') ?></th>
                            <th><?= __('TC Solicitado') ?></th>
                            <th><?= __('Instituição') ?></th>
                            <th><?= __('Supervisor') ?></th>
                            <th><?= __('Professor') ?></th>
                            <th><?= __('Período') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('CH') ?></th>
                        </tr>
                        <?php foreach ($supervisor->estagiarios as $estagiario) : ?>
                            <tr>
                                <td class="actions">
                                    <?= $this->Html->link(__('Ver'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?>
                                    <?php if ($user_data['administrador_id']) : ?>
                                        <?= $this->Html->link(__('Editar'), ['controller' => 'Estagiarios', 'action' => 'edit', $estagiario->id]) ?>
                                        <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Estagiarios', 'action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario_{0}?', $estagiario->id)]) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($estagiario->id) ?></td>
                                <td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                                <td><?= h($estagiario->registro) ?></td>
                                <td><?= empty($estagiario->aluno->turno) ? __('N/A') : h($estagiario->aluno->turno->turno) ?></td>
                                <td><?= h($estagiario->nivel) ?></td>
                                <td><?= h($estagiario->tc_assinado) == '1' ? __('Sim') : __('Não') ?></td>
                                <td><?= $estagiario->tc_solicitacao ? $estagiario->tc_solicitacao->format('d/m/Y') : '' ?></td>
                                <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                                <td><?= ($estagiario->supervisor and $estagiario->supervisor->nome) ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                                <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                                <td><?= h($estagiario->periodo) ?></td>
                                <td><?= $estagiario->nota ? $this->Number->format($estagiario->nota) : '' ?></td>
                                <td><?= $estagiario->ch ? $this->Number->format($estagiario->ch) : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        </div>            
    </div>
</div>