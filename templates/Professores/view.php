<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor $professor
 */

?>
<div>
    <div class="column-responsive column-80">
        <div class="professores view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Professores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Professor'), ['action' => 'edit', $professor->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Professor'), ['action' => 'delete', $professor->id], ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Novo Professor'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>professor_<?= h($professor->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($professor->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nome') ?></th>
                    <td><?= h($professor->nome) ?></td>
                </tr>
                <tr>
                    <th><?= __('Cpf') ?></th>
                    <td><?= h($professor->cpf) ?></td>
                </tr>
                <tr>
                    <th><?= __('Localnascimento') ?></th>
                    <td><?= h($professor->localnascimento) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <td><?= $professor->telefone ? h('(' . $professor->ddd_telefone . ') ' . $professor->telefone) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Celular') ?></th>
                    <td><?= $professor->celular ? h('(' . $professor->ddd_celular . ') ' . $professor->celular) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= $professor->email ? $this->Text->autoLinkEmails($professor->email) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Homepage') ?></th>
                    <td><?= h($professor->homepage) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rede social') ?></th>
                    <td><?= h($professor->redesocial) ?></td>
                </tr>
                <tr>
                    <th><?= __('Curriculo lattes') ?></th>
                    <td><?= $professor->curriculolattes ? $this->Html->link('http://lattes.cnpq.br/' . h($professor->curriculolattes)) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Curriculo sigma') ?></th>
                    <td><?= h($professor->curriculosigma) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pesquisador DGP') ?></th>
                    <td><?= h($professor->pesquisadordgp) ?></td>
                </tr>
                <tr>
                    <th><?= __('Formacao profissional') ?></th>
                    <td><?= h($professor->formacaoprofissional) ?></td>
                </tr>
                <tr>
                    <th><?= __('Graduacao Universidade') ?></th>
                    <td><?= h($professor->universidadedegraduacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de formacao') ?></th>
                    <td><?= h($professor->anoformacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mestrado area') ?></th>
                    <td><?= h($professor->mestradoarea) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mestrado universidade') ?></th>
                    <td><?= h($professor->mestradouniversidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de conclusao do Mestrado') ?></th>
                    <td><?= h($professor->mestradoanoconclusao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Doutorado area') ?></th>
                    <td><?= h($professor->doutoradoarea) ?></td>
                </tr>
                <tr>
                    <th><?= __('Doutorado universidade') ?></th>
                    <td><?= h($professor->doutoradouniversidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de conclusao do Doutorado') ?></th>
                    <td><?= h($professor->doutoradoanoconclusao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Forma de ingresso') ?></th>
                    <td><?= h($professor->formaingresso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tipo do cargo') ?></th>
                    <td><?= h($professor->tipocargo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Regimetrabalho') ?></th>
                    <td><?= h($professor->regimetrabalho) ?></td>
                </tr>
                <tr>
                    <th><?= __('Departamento') ?></th>
                    <td><?= h($professor->departamento) ?></td>
                </tr>
                <tr>
                    <th><?= __('Motivo egresso') ?></th>
                    <td><?= h($professor->motivoegresso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Siape') ?></th>
                    <td><?= h($professor->siape) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data de nascimento') ?></th>
                    <td><?= h($professor->datanascimento) ?></td>
                </tr>
                <tr>
                    <th><?= __('Atualizacao lattes') ?></th>
                    <td><?= h($professor->atualizacaolattes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data ingresso') ?></th>
                    <td><?= h($professor->dataingresso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data egresso') ?></th>
                    <td><?= h($professor->dataegresso) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Observacoes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($professor->observacoes)); ?>
                </blockquote>
            </div>
            
            <?php if (!empty($professor->user)) : ?>
            <div class="related">
                <h4><?= __('Related User') ?></h4>
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
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Users', 'action' => 'view', $professor->user->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Users', 'action' => 'edit', $professor->user->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Users', 'action' => 'delete', $professor->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $professor->user->id)]) ?>
                            </td>
                            <td><?= h($professor->user->id) ?></td>
                            <td><?= $professor->user->email ? $this->Text->autoLinkEmails($professor->user->email) : '' ?></td>
                            <td><?= h($professor->user->registro) ?></td>
                            <td><?= $professor->user->timestamp ? h($professor->user->timestamp) : '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($estagiarios)) : ?>
            <div class="related">
                <h4><?= __('Related Estagiarios') ?></h4>
                <div class="paginator">
                    <?= $this->element('paginator'); ?>
                </div>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Aluno') ?></th>
                            <th><?= __('Registro') ?></th>
                            <th><?= __('Turno') ?></th>
                            <th><?= __('Nivel') ?></th>
                            <th><?= __('Tc') ?></th>
                            <th><?= __('Tc Solicitacao') ?></th>
                            <th><?= __('Instituicao') ?></th>
                            <th><?= __('Supervisor') ?></th>
                            <th><?= __('Periodo') ?></th>
                            <th><?= __('Turma') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('Ch') ?></th>
                        </tr>

                        <?php foreach ($estagiarios as $estagiario) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Estagiarios', 'action' => 'edit', $estagiario->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Estagiarios', 'action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario_{0}?', $estagiario->id)]) ?>
                            </td>
                            <td><?= h($estagiario->id) ?></td>
                            <td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
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
                            <td><?= h($estagiario->nivel) ?></td>
                            <td><?= h($estagiario->tc) ?></td>
                            <td><?= h($estagiario->tc_solicitacao) ?></td>
                            <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                            <td><?= $estagiario->supervisor ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                            <td><?= h($estagiario->periodo) ?></td>
                            <td><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></td>
                            <td><?= h($estagiario->nota) ?></td>
                            <td><?= h($estagiario->ch) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                    </table>
                </div>
                <div class="paginator">
                    
                    <?= $this->element('paginator'); ?>
                    <?= $this->element('paginator_count'); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($professor->muralestagios)) : ?>
            <div class="related">
                <h4><?= __('Related Muralestagios') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Instituicao') ?></th>
                            <th><?= __('Vagas') ?></th>
                            <th><?= __('Beneficios') ?></th>
                            <th><?= __('Fim De Semana') ?></th>
                            <th><?= __('CargaHoraria') ?></th>
                            <th><?= __('DataSelecao') ?></th>
                            <th><?= __('DataInscricao') ?></th>
                        </tr>
                        <?php foreach ($professor->muralestagios as $muralestagio) : ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Muralestagios', 'action' => 'view', $muralestagio->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'Muralestagios', 'action' => 'edit', $muralestagio->id]) ?>
                                <?= $this->Form->postLink(__('Deletar'), ['controller' => 'Muralestagios', 'action' => 'delete', $muralestagio->id], ['confirm' => __('Are you sure you want to delete muralestagios_{0}?', $muralestagio->id)]) ?>
                            </td>
                            <td><?= h($muralestagio->id) ?></td>
                            <td><?= $muralestagio->instituicao ? $this->Html->link($muralestagio->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $muralestagio->instituicao->id]) : '' ?></td>
                            <td><?= h($muralestagio->vagas) ?></td>
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
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
