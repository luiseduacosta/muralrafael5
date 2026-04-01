<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor $professor
 */
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $this->request->getAttribute('identity')->getOriginalData();
}
?>

<?php
// May be this is a temporary solution. Put into de Configuracoes table in json data format is a better solution
$departamentos = [
    'Fundamentos' => 'Fundamentos',
    'Métodos e técnicas' => 'Metodologia',
    'Política social' => 'Politicas',
]
?>

<div>
    <div class="column-responsive column-80">
        <div class="professores view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?= $this->Html->link(__('Listar Professores'), ['action' => 'index'], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Editar Professor(a)'), ['action' => 'edit', $professor->id], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Form->postLink(__('Excluir Professor(a)'), ['action' => 'delete', $professor->id], ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome), 'class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('Novo(a) Professor(a)'), ['action' => 'add'], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('CH e nota'), ['controller' => 'Estagiarios', 'action' => 'lancanota', '?' => ['professor_id' => $professor->id]], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?php endif; ?>
                    <?php if ($user_data['professor_id'] && ($professor->id == $user_data['professor_id'])) : ?>
                        <?= $this->Html->link(__('Editar Professor(a)'), ['action' => 'edit', $professor->id], ['class' => 'button mb-1', 'style' => 'width: 20%;']) ?>
                        <?= $this->Html->link(__('CH e nota'), ['controller' => 'Estagiarios', 'action' => 'lancanota', '?' => ['professor_id' => $professor->id]], ['class' => 'button mb-1' , 'style' => 'width: 20%;']) ?>
                    <?php endif; ?>
                </div>
            </aside>

            <h3><?= __('Professor(a)') ?> <?= h($professor->nome) ?></h3>

            <ul class='nav nav-tabs mb-3' id='professorTabs' role='tablist'>
                <li class='nav-item' role='presentation'>
                    <button class='nav-link active' id='pessoais-tab' data-bs-toggle='tab' data-bs-target='#tab-1' type='button' role='tab' aria-controls='tab-1' aria-selected='true'>Dados Pessoais</button>
                </li>
                <li class='nav-item' role='presentation'>
                    <button class='nav-link' id='academicos-tab' data-bs-toggle='tab' data-bs-target='#tab-2' type='button' role='tab' aria-controls='tab-2' aria-selected='false'>Dados Acadêmicos</button>
                </li>
                <li class='nav-item' role='presentation'>
                    <button class='nav-link' id='funcionais-tab' data-bs-toggle='tab' data-bs-target='#tab-3' type='button' role='tab' aria-controls='tab-3' aria-selected='false'>Dados Funcionais</button>
                </li>
            </ul>

            <div class='tab-content' id='professorTabsContent'> 
                <div id='tab-1' class='tab-pane fade show active' role='tabpanel' aria-labelledby='pessoais-tab'>
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
                    <th><?= __('CPF') ?></th>
                    <td><?= h($professor->cpf) ?></td>
                </tr>
                <tr>
                    <th><?= __('CRESS') ?></th>
                    <td><?php if (empty($professor->cress))
                            echo '';
                        else
                            echo h($professor->cress) . ' ' . $professor->regiao . ' região' ?></td>
                </tr>
                <tr>
                    <th><?= __('Siape') ?></th>
                    <td><?= h($professor->siape) ?></td>
                </tr>
               <tr>
                    <th><?= __('Data de nascimento') ?></th>
                    <td><?= empty($professor->datanascimento) ? '' : $professor->datanascimento->format('d/m/Y') ?></td>
                </tr>
                <tr> 
                    <th><?= __('Local de nascimento') ?></th>
                    <td><?= empty($professor->localnascimento) ? '' : h($professor->localnascimento) ?></td>
                </tr>
                <tr>
                    <th><?= __('Telefone') ?></th>
                    <?php if (strlen($professor->telefone) < 10) : ?>
                        <td><?= $professor->telefone ? h('(' . $professor->ddd_telefone . ') ' . $professor->telefone) : '' ?></td>
                    <?php else : ?>
                        <td><?= empty($professor->telefone) ? '' : h($professor->telefone) ?></td>
                    <?php endif ?>    
                </tr>
                <tr>
                    <th><?= __('Celular') ?></th>
                    <?php if (strlen($professor->celular) < 10) : ?>
                        <td><?= $professor->celular ? h('(' . $professor->ddd_celular . ') ' . $professor->celular) : '' ?></td>
                    <?php else : ?>
                        <td><?= empty($professor->celular) ? '' : h($professor->celular) ?></td>
                    <?php endif ?>    
                    </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= empty($professor->email) ? '' : $this->Text->autoLinkEmails($professor->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Home page') ?></th>
                    <td><?= empty($professor->homepage) ? '' : h($professor->homepage) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rede social') ?></th>
                    <td><?= empty($professor->redesocial) ? '' : h($professor->redesocial) ?></td>
                </tr>
                <tr>
                    <th><?= __('Curriculo lattes') ?></th>
                    <td><?= empty($professor->curriculolattes) ? '' : $this->Html->link('http://lattes.cnpq.br/' . h($professor->curriculolattes)) ?></td>
                </tr>
                <tr>
                    <th><?= __('Atualização lattes') ?></th>
                    <td><?= empty($professor->atualizacaolattes) ? '' : $professor->atualizacaolattes->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <th><?= __('Curriculo sigma') ?></th>
                    <td><?= empty($professor->curriculosigma) ? '' : h($professor->curriculosigma) ?></td>
                </tr>
                <tr>
                    <th><?= __('Diretorio de Grupos de Pesquisa') ?></th>
                    <td><?= h($professor->pesquisadordgp) ?></td>
                </tr>
                </table>
                </div>

                <div id='tab-2' class='tab-pane fade' role='tabpanel' aria-labelledby='academicos-tab'>
                <table>
                <tr>
                    <th><?= __('Formação profissional') ?></th>
                    <td><?= h($professor->formacaoprofissional) ?></td>
                </tr>
                <tr>
                    <th><?= __('Graduação Universidade') ?></th>
                    <td><?= h($professor->universidadedegraduacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de formação') ?></th>
                    <td><?= h($professor->anoformacao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mestrado área') ?></th>
                    <td><?= h($professor->mestradoarea) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mestrado universidade') ?></th>
                    <td><?= h($professor->mestradouniversidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de conclusão do Mestrado') ?></th>
                    <td><?= h($professor->mestradoanoconclusao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Doutorado área') ?></th>
                    <td><?= h($professor->doutoradoarea) ?></td>
                </tr>
                <tr>
                    <th><?= __('Doutorado universidade') ?></th>
                    <td><?= h($professor->doutoradouniversidade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Ano de conclusão do Doutorado') ?></th>
                    <td><?= h($professor->doutoradoanoconclusao) ?></td>
                </tr>
                </table>
                </div>
                
                <div id='tab-3' class='tab-pane fade' role='tabpanel' aria-labelledby='funcionais-tab'>
                <table>
                <tr>
                    <th><?= __('Departamento') ?></th>
                    <td><?= h($professor->departamento) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data de ingresso') ?></th>
                    <td><?= empty($professor->dataingresso) ? '' : $professor->dataingresso->format('d/m/Y') ?></td>
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
                    <th><?= __('Regime de trabalho') ?></th>
                    <td><?= h($professor->regimetrabalho) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data de egresso') ?></th>
                    <td><?= empty($professor->dataegresso) ? '' : $professor->dataegresso->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <th><?= __('Motivo egresso') ?></th>
                    <td><?= h($professor->motivoegresso) ?></td>
                </tr>
                </table>
                    <div class="text">
                        <strong><?= __('Observações') ?></strong>
                        <blockquote>
                            <?= $this->Markdown->parse($professor->observacoes); ?>
                        </blockquote>
                    </div>
                </div>    
            </div>
    
            <?php if (!empty($professor->user)) : ?>
            <div class="related">
                <h4><?= __('User') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Número') ?></th>
                            <th><?= __('Atualização') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'Users', 'action' => 'view', $professor->user->id]) ?>
                                <?php if ($user_data['administrador_id']) : ?>
                                    <?= $this->Html->link(__('Editar'), ['controller' => 'Users', 'action' => 'edit', $professor->user->id]) ?>
                                    <?= $this->Form->postLink(__('Excluir'), ['controller' => 'Users', 'action' => 'delete', $professor->user->id], ['confirm' => __('Are you sure you want to delete user_{0}?', $professor->user->id)]) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= h($professor->user->id) ?></td>
                            <td><?= $professor->email ? $this->Text->autoLinkEmails($professor->email) : '' ?></td>
                            <td><?= h($professor->user->numero) ?></td>
                            <td><?= $professor->user->timestamp ? h($professor->user->timestamp) : '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($estagiarios)) : ?>
            <div class="related">
                <h4><?= __('Estagiarios') ?></h4>
                <div class="paginator">
                    <?php echo $this->element('paginator'); ?>
                </div>
                <div class="table_wrap">
                    <table style="table-layout: auto; width: auto;">
                    <tr>
                            <th class="actions"><?= __('Ações') ?></th> 
                            <th><?= __('Id') ?></th>
                            <th><?= __('Aluno(a)') ?></th>
                            <th><?= __('Registro') ?></th>
                            <th><?= __('Turno') ?>
                            <th><?= __('Nível') ?>
                            <th><?= __('TC assinado') ?>
                            <th><?= __('TC solicitado') ?>
                            <th><?= __('Instituição') ?></th>
                            <th><?= __('Supervisor(a)') ?></th>
                            <th><?= __('Período') ?></th>
                            <th><?= __('Nota') ?></th>
                            <th><?= __('CH') ?></th>
                        </tr>

                        <?php foreach ($estagiarios as $estagiario) : ?>
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
                            <td style="min-width: 80px; max-width: 120px;"><?= h($estagiario->aluno->turno->turno ?? '') ?></td>
                            <td><?= h($estagiario->nivel) ?></td>
                            <td><?= $estagiario->tc ='1' ? 'Sim' : 'Não' ?></td>
                            <td><?= $estagiario->tc_solicitacao ? $estagiario->tc_solicitacao->format('d/m/Y') : '' ?></td>
                            <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                            <td><?= $estagiario->supervisor ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                            <td><?= h($estagiario->periodo) ?></td>
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
                        
        </div>
    </div>
</div>
