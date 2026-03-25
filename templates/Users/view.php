<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
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
        <div class="users view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Email'), ['action' => 'edit', $user->id], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Senha'), ['action' => 'editpassword', $user->id], ['class' => 'button']) ?> 
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Listar Usuários'), ['action' => 'index'], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Usuário'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete {0}?', $user->email), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Novo Usuário'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?php endif; ?>
                    
                </div>
            </aside>
            <h3>user_<?= h($user->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Categoria') ?></th>
                    <td><?= h($user->categoria) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($user->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= $user->email ? $this->Text->autoLinkEmails($user->email) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro (DRE, Siape ou CRESS)') ?></th>
                    <td><?= h($user->numero) ?></td>
                </tr>
                <tr>
                    <th><?= __('Criado') ?></th>
                    <td><?= h($user->timestamp?->format('d/m/Y H:i:s')) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modificado') ?></th>
                    <td><?= h($user->modified?->format('d/m/Y H:i:s')) ?></td>
                </tr>
            </table>

            <?php if ($user->categoria == '1') : ?>
            <div class="related">
                <h4><?= __('Administrador') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nome') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Editar'), ['controller' => 'administradores', 'action' => 'edit', $user->id]) ?>
                                <?= $this->Form->postLink(__('Excluir'), ['controller' => 'administradores', 'action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete administrador_{0}?', $user->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$user->id, ['controller' => 'administradores', 'action' => 'view', '?' => ['user_id' => $user->id]]) ?></td>
                            <td><?= h($user->administrador->nome ?? 'Não informado') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (($user->categoria == '2' && $user_data['aluno_id'] == $user->id)) : ?>
            <div class="related">
                <h4><?= __('Aluno') ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Registro') ?></th>
                            <th><?= __('Ingresso') ?></th>
                            <th><?= __('Telefone') ?></th>
                            <th><?= __('Celular') ?></th>
                            <th><?= __('CPF') ?></th>
                            <th><?= __('Nascimento') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'alunos', 'action' => 'view', $user->aluno->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'alunos', 'action' => 'edit', $user->aluno->id]) ?>
                                <?php if ($user_data['administrador_id']) : ?>
                                    <?= $this->Form->postLink(__('Excluir'), ['controller' => 'alunos', 'action' => 'delete', $user->aluno->id], ['confirm' => __('Are you sure you want to delete aluno_{0}?', $user->aluno->id)]) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $this->Html->link((string)$user->aluno->id, ['controller' => 'alunos', 'action' => 'view', $user->aluno->id]) ?></td>
                            <td><?= h($user->aluno->nome) ?></td>
                            <td><?= h($user->aluno->registro) ?></td>
                            <td><?= h($user->aluno->ingresso) ?></td>
                            <td><?= '(' . h($user->aluno->codigo_telefone) . ') ' . h($user->aluno->telefone) ?></td>
                            <td><?= '(' . h($user->aluno->codigo_celular) . ') ' . h($user->aluno->celular) ?></td>
                            <td><?= h($user->aluno->cpf) ?></td>
                            <td><?= h($user->aluno->nascimento) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php elseif ($user->categoria == '1') : ?>
                <p><?= $this->Html->link('Adicionar aluno', ['controller' => 'alunos', 'action' => 'add'], ['class' => 'button btn-info']) ?></p>
            <?php endif; ?>

            <?php if ($user->categoria == '3' && !empty($user->professor->id)) : ?>
            <div class="related">
                <h4><?= __('Professor ' . $user->professor_id) ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Siape') ?></th>
                            <th><?= __('Celular') ?></th>
                            <th><?= __('Lattes') ?></th>
                            <th><?= __('Departamento') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'professores', 'action' => 'view', $user->professor->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'professores', 'action' => 'edit', $user->professor->id]) ?>
                                <?= $this->Form->postLink(__('Excluir'), ['controller' => 'professores', 'action' => 'delete', $user->professor->id], ['confirm' => __('Are you sure you want to delete professor_{0}?', $user->professor->id)]) ?>
                            </td>
                            <td><?= $this->Html->link((string)$user->professor->id, ['controller' => 'professores', 'action' => 'view', $user->professor->id]) ?></td>
                            <td><?= $this->Html->link(h($user->professor->nome ?? 'Não informado'), ['controller' => 'professores', 'action' => 'view', $user->professor->id]) ?></td>
                            <td><?= $user->professor->siape ?></td>
                            <td>
                                <?php
                                if (strlen($user->professor->celular) < 9) {
                                    echo '(' . h($user->professor->ddd_celular) . ') ' . h($user->professor->celular);
                                } else {
                                    echo $user->professor->celular;
                                }
                                ?>
                            </td>
                            <td><?= $user->professor->curriculolattes ? $this->Html->link('http://lattes.cnpq.br/' . h($user->professor->curriculolattes)) : '' ?></td>
                            <td><?= h($user->professor->departamento ?? 'Não informado') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php elseif ($user->categoria == '1') : ?>
                <p><?= $this->Html->link('Adicionar professor', ['controller' => 'professores', 'action' => 'add'], ['class' => 'button btn-info']) ?></p>
            <?php endif; ?>

            <?php if ($user->categoria == '4' && !empty($user->supervisor->id)) : ?>
            <div class="related">
                <h4><?= __('Supervisor ' . $user->supervisor_id) ?></h4>
                <div class="table_wrap">
                    <table>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('CPF') ?></th>
                            <th><?= __('CRESS') ?></th>
                            <th><?= __('Região') ?></th>
                        </tr>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['controller' => 'supervisores', 'action' => 'view', $user->supervisor->id]) ?>
                                <?= $this->Html->link(__('Editar'), ['controller' => 'supervisores', 'action' => 'edit', $user->supervisor->id]) ?>
                                <?php if ($user_data['administrador_id']) : ?>
                                    <?= $this->Form->postLink(__('Excluir'), ['controller' => 'supervisores', 'action' => 'delete', $user->supervisor->id], ['confirm' => __('Are you sure you want to delete supervisor_{0}?', $user->supervisor->id)]) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $this->Html->link((string)$user->supervisor->id, ['action' => 'view', $user->supervisor->id]) ?></td>
                            <td><?= $this->Html->link($user->supervisor->nome, ['action' => 'view', $user->supervisor->id]) ?></td>
                            <td><?= h($user->supervisor->cpf) ?></td>
                            <td><?= h($user->supervisor->cress) ?></td>
                            <td><?= h($user->supervisor->regiao) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php elseif ($user->categoria == '1') : ?>
                <p><?= $this->Html->link('Adicionar supervisor', ['controller' => 'supervisores', 'action' => 'add'], ['class' => 'button btn-info']) ?></p>
            <?php endif; ?>
            
        </div>
    </div>
</div>
