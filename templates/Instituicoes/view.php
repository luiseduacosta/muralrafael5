<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao $instituicao
 */
//pr($instituicao);
//die();
?>
<div>
    <div class="column-responsive column-80">
        <div class="instituicoes view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Instituições'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Instituição'), ['action' => 'edit', $instituicao->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Instituição'), ['action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete {0}?', $instituicao->instituicao), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Nova Instituição'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>instituicao_<?= h($instituicao->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($instituicao->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituicao') ?></th>
                    <td><?= $instituicao->instituicao ?></td>
                </tr>
                <tr>
                    <th><?= __('Area') ?></th>
                    <td><?= $instituicao->area ? $this->Html->link(''.$instituicao->area->area, ['controller' => 'Areas', 'action' => 'view', $instituicao->area->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Natureza') ?></th>
                    <td><?= h($instituicao->natureza) ?></td>
                </tr>
                <tr>
                    <th><?= __('CNPJ') ?></th>
                    <td><?= h($instituicao->cnpj) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= $instituicao->email ? $this->Text->autoLinkEmails($instituicao->email) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Url') ?></th>
                    <td><?= $instituicao->url ? $this->Html->link($instituicao->url) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Endereco') ?></th>
                    <td><?= h($instituicao->endereco . ' - ' . $instituicao->bairro . ' - ' .  $instituicao->municipio . ' - ' . $instituicao->cep ) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <td><?= h($instituicao->telefone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Beneficio') ?></th>
                    <td><?= h($instituicao->beneficio) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fim De Semana') ?></th>
                    <td>
                        <?php
                        $fim_de_semana = '';
                        switch ( $instituicao->fim_de_semana ) {
                            case 0: $fim_de_semana = 'Não';          break;
                            case 1: $fim_de_semana = 'Sim';          break;
                            case 2: $fim_de_semana = 'Parcialmente'; break;
                        }
                        echo $fim_de_semana;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Local Inscricao') ?></th>
                    <td><?= h($instituicao->local_inscricao) ? "Inscrição somente no mural da Coordenação de Estágio da ESS" : "Inscrição na Instituição e no mural da Coordenação de Estágio da ESS" ?></td>
                </tr>
                <tr>
                    <th><?= __('Seguro') ?></th>
                    <td><?= h($instituicao->seguro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Avaliacao') ?></th>
                    <td><?= h($instituicao->avaliacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Observacoes') ?></th>
                    <td><?= h($instituicao->observacoes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Convenio') ?></th>
                    <td><?= h($instituicao->convenio) ?></td>
                </tr>
                <tr>
                    <th><?= __('Expira') ?></th>
                    <td><?= h($instituicao->expira) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Supervisores') ?></h4>
                <?php if (!empty($instituicao->supervisores)) : ?>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('CPF') ?></th>
                            <th><?= __('CRESS') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Escola') ?></th>
                            <th><?= __('Ano Formatura') ?></th>
                        </tr>
                        <?php foreach ($instituicao->supervisores as $supervisores) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Supervisores', 'action' => 'view', $supervisores->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Supervisores', 'action' => 'edit', $supervisores->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Supervisores', 'action' => 'delete', $supervisores->id], ['confirm' => __('Are you sure you want to delete # {0}?', $supervisores->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$supervisores->id, ['controller' => 'Supervisores', 'action' => 'view', $supervisores->id]) ?></td>
                            <td><?= $this->Html->link($supervisores->nome, ['controller' => 'Supervisores', 'action' => 'view', $supervisores->id]) ?></td>
                            <td><?= h($supervisores->cpf) ?></td>
                            <td><?= h($supervisores->cress) ?></td>
                            <td><?= $supervisores->user ? $this->Text->autoLinkEmails($supervisores->user->email) : '' ?></td>
                            <td><?= h($supervisores->escola) ?></td>
                            <td><?= h($supervisores->ano_formatura) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Estagiarios') ?></h4>
                <?php if (!empty($instituicao->estagiarios)) : ?>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Aluno') ?></th>
                            <th><?= __('Registro') ?></th>
                            <th><?= __('Turno') ?></th>
                            <th><?= __('Instituicao') ?></th>
                            <th><?= __('Supervisor') ?></th>
                            <th><?= __('Professor') ?></th>
                            <th><?= __('Periodo') ?></th>
                            <th><?= __('Turma estagio') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('Ch') ?></th>
                        </tr>
                        <?php foreach ($instituicao->estagiarios as $estagiario) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Estagiarios', 'action' => 'edit', $estagiario->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Estagiarios', 'action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete # {0}?', $estagiario->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$estagiario->id, ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?></td>
                            <td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                            <td><?= h($estagiario->registro) ?></td>
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
                            <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                            <td><?= $estagiario->supervisor ? $this->Html->link(h($estagiario->supervisor->nome), ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                            <td><?= $estagiario->professor ? $this->Html->link(h($estagiario->professor->nome), ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                            <td><?= h($estagiario->periodo) ?></td>
                            <td><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></td>
                            <td><?= h($estagiario->nota) ?></td>
                            <td><?= h($estagiario->ch) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Muralestagios') ?></h4>
                <?php if (!empty($instituicao->muralestagios)) : ?>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Vagas') ?></th>
                            <th><?= __('Professor') ?></th>
                            <th><?= __('Beneficios') ?></th>
                            <th><?= __('Final De Semana') ?></th>
                            <th><?= __('CargaHoraria') ?></th>
                            <th><?= __('DataSelecao') ?></th>
                            <th><?= __('DataInscricao') ?></th>
                        </tr>
                        <?php foreach ($instituicao->muralestagios as $muralestagio) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Muralestagios', 'action' => 'view', $muralestagio->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Muralestagios', 'action' => 'edit', $muralestagio->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Muralestagios', 'action' => 'delete', $muralestagio->id], ['confirm' => __('Are you sure you want to delete # {0}?', $muralestagio->id)]) ?>
                            </td>
                            <td><?= $this->Html->link($muralestagio->id, ['controller' => 'Muralestagios', 'action' => 'view', $muralestagio->id]) ?></td>
                            <td><?= h($muralestagio->vagas) ?></td>
                            <td><?= $muralestagio->professor ? $this->Html->link($muralestagio->professor->nome, ['controller' => 'Professores', 'action' => 'view', $muralestagio->professor->id]) : '' ?></td>
                            <td><?= h($muralestagio->beneficios) ?></td>
                            <td>
                                <?php
                                $fim_de_semana = '';
                                switch ( $muralestagio->fim_de_semana ) {
                                    case 0: $fim_de_semana = 'Não';          break;
                                    case 1: $fim_de_semana = 'Sim';          break;
                                    case 2: $fim_de_semana = 'Parcialmente'; break;
                                }
                                echo $fim_de_semana;
                                ?>
                            </td>
                            <td><?= h($muralestagio->cargaHoraria) ?></td>
                            <td><?= h($muralestagio->dataSelecao) ?></td>
                            <td><?= h($muralestagio->dataInscricao) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($instituicao->visitas)) : ?>
            <div class="related">
                <h4><?= __('Related Visitas') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Data') ?></th>
                            <th><?= __('Motivo') ?></th>
                            <th><?= __('Responsavel') ?></th>
                            <th><?= __('Descricao') ?></th>
                            <th><?= __('Avaliacao') ?></th>
                        </tr>
                        <?php foreach ($instituicao->visitas as $visita) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Visitas', 'action' => 'view', $visita->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Visitas', 'action' => 'edit', $visita->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Visitas', 'action' => 'delete', $visita->id], ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id)]) ?>
                            </td>
                            <td><?= $this->Html->link($visita->id, ['action' => 'view', $visita->id]) ?></td>
                            <td><?= h($visita->data) ?></td>
                            <td><?= h($visita->motivo) ?></td>
                            <td><?= h($visita->responsavel) ?></td>
                            <td><?= h($visita->descricao) ?></td>
                            <td><?= h($visita->avaliacao) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
