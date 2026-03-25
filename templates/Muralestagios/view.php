<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muralestagio $muralestagio
 */

declare(strict_types=1);

use Cake\I18n\Date;

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

$hoje = Date::now();
// Se não houver data de inscrição, assume que está aberto (amanhã) para fins de exibição
$prazo = $muralestagio->dataInscricao ?? $hoje->addDays(1);
?>

<div>
    <div class="column-responsive column-80">
        <div class="muralestagios view content">

            <?php if ($prazo->isFuture() || $prazo->isToday()) : ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= __('Inscrições abertas até {0}', $prazo->format('d/m/Y')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Mural estagios'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Editar estagio'), ['action' => 'edit', $muralestagio->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir estagio'), ['action' => 'delete', $muralestagio->id], ['confirm' => __('Are you sure you want to delete # {0}?', $muralestagio->id), 'class' => 'button']) ?>
                        <?= $this->Html->link(__('Novo estagio'), ['action' => 'add'], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Imprimir inscrições'), ['controller' => 'Muralestagios', 'action' => 'imprimepdf', $muralestagio->id], ['class' => 'button']) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <h3>estagio_<?= h($muralestagio->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($muralestagio->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Instituição') ?></th>
                    <td><?= $muralestagio->instituicao_entidade ? $this->Html->link($muralestagio->instituicao_entidade->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $muralestagio->instituicao_entidade->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Convênio') ?></th>
                    <td>
                        <?= $muralestagio->convenio ? 'Sim' : 'Não'; ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= $muralestagio->email ? $this->Text->autoLinkEmails($muralestagio->email) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Vagas') ?></th>
                    <td><?= $this->Number->format($muralestagio->vagas) ?></td>
                </tr>
                <tr>
                    <th><?= __('Beneficios') ?></th>
                    <td><?= h($muralestagio->beneficios) ?></td>
                </tr>
                <tr>
                    <th><?= __('Final De Semana') ?></th>
                    <td>
                        <?php
                        $fim_de_semana = '';
                        switch ($muralestagio->fim_de_semana) {
                            case 0:
                                $fim_de_semana = 'Não';
                                break;
                            case 1:
                                $fim_de_semana = 'Sim';
                                break;
                            case 2:
                                $fim_de_semana = 'Parcialmente';
                                break;
                        }
                        echo $fim_de_semana;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Carga Horária') ?></th>
                    <td><?= $this->Number->format($muralestagio->carga_horaria) ?></td>
                </tr>
                <tr>
                    <th><?= __('Requisitos') ?></th>
                    <td><?= $muralestagio->requisitos ?></td>
                </tr>
                <tr>
                    <th><?= __('Local Inscrição') ?></th>
                    <td><?= h($muralestagio->local_inscricao) ? 'Inscrição somente no mural da Coordenação de Estágio da ESS' : 'Inscrição na Instituição e no mural da Coordenação de Estágio da ESS' ?></td>
                </tr>
                <tr>
                    <th><?= __('Data de encerramento das inscrições') ?></th>
                    <td><?= $muralestagio->data_inscricao ? $muralestagio->data_inscricao->format('d/m/Y') : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Local da Seleção') ?></th>
                    <td><?= h($muralestagio->local_selecao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data da Seleção') ?></th>
                    <td><?= $muralestagio->data_selecao ? $muralestagio->data_selecao->format('d/m/Y') : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Horário da Seleção') ?></th>
                    <td><?= h($muralestagio->horario_selecao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Forma de Seleção') ?></th>
                    <td>
                        <?php
                        $forma_selecao = '';
                        switch (h($muralestagio->forma_selecao)) {
                            case 0:
                                $forma_selecao = 'Entrevista';
                                break;
                            case 1:
                                $forma_selecao = 'CR';
                                break;
                            case 2:
                                $forma_selecao = 'Prova';
                                break;
                            case 3:
                                $forma_selecao = 'Outra';
                                break;
                        }
                        echo $forma_selecao;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Contato') ?></th>
                    <td><?= h($muralestagio->contato) ?></td>
                </tr>
                <tr>
                    <th><?= __('Periodo') ?></th>
                    <td><?= h($muralestagio->periodo) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Outras informações') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph($muralestagio->outras); ?>
                </blockquote>
            </div>

            <?php if ($user_data['administrador_id']) : ?>
                <tr>
                    <td colspan = '2' class="text-center">
                        <?php echo $this->Html->link('Admin inscrição', ['controller' => 'Inscricoes', 'action' => 'add', '?' => ['muralestagio_id' => $muralestagio->id]], ['role' => 'button', 'class' => ' button btn-primary']); ?>
                    </td>
                </tr>
            <?php elseif ($user_data['aluno_id']) : ?>
                <!-- if dataInscricao is empty them let the close day of application open //-->
                <?php if (empty($muralestagio->data_inscricao)) {
                    $muralestagio->data_inscricao = new Date('tomorrow');
                } ?>
                <?php if ($muralestagio->data_inscricao->isFuture() || $muralestagio->data_inscricao->isToday()) : ?>
                    <!--
                    Se a inscricao eh na instituição também tem que fazer inscrição no mural
                    //-->
                    <?php if ((string)$muralestagio['local_inscricao'] === '1') : ?>
                        <tr>
                            <td colspan = 2>
                                <p class="text-center text-danger">Não esqueça de também fazer inscrição na instituição. Ambas são necessárias!</p>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <tr>
                        <td colspan = 2 class="text-center">
                            <?php echo $this->Html->link('Fazer inscrição', ['controller' => 'Inscricoes', 'action' => 'add', '?' => ['muralestagio_id' => $muralestagio->id]], ['role' => 'button', 'class' => 'button btn-primary']); ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td colspan = 2>
                            <button class="button btn-danger">Inscrições encerradas!</button>
                        </td>
                    </tr>
                <?php endif; ?>

            <?php endif; ?>

            <?php if ($user_data['administrador_id'] || $user_data['aluno_id'] || $user_data['professor_id']) : ?>
                <?php if (!empty($muralestagio->inscricoes)) : ?>
                <div class="related">
                    <h4><?= __('Inscrições') ?></h4>
                    <div class="table_wrap">
                        <table>
                            <tr>
                                <th class="actions"><?= __('Actions') ?></th>
                                <th><?= __('Id') ?></th>
                                <th><?= __('Registro') ?></th>
                                <th><?= __('Aluno') ?></th>
                                <th><?= __('Data') ?></th>
                                <th><?= __('Periodo') ?></th>
                                <th><?= __('Timestamp') ?></th>
                            </tr>
                            <?php foreach ($muralestagio->inscricoes as $inscricao) : ?>
                            <tr>
                                <td class="actions">
                                    <?= $this->Html->link(__('Ver'), ['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]) ?>
                                        <?php if ($user_data['administrador_id']) : ?>
                                            <?= $this->Html->link(__('Editar'), ['controller' => 'Inscricoes', 'action' => 'edit', $inscricao->id]) ?>
                                        <?php endif; ?>
                                </td>
                                <td><?= h($inscricao->id) ?></td>
                                <td><?= $inscricao->aluno ? h($inscricao->aluno->registro) : '' ?></td>
                                <td><?= $inscricao->aluno ? $this->Html->link($inscricao->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $inscricao->aluno->id])  : '' ?></td>
                                <td><?= $inscricao->data ? $inscricao->data->format('d/m/Y') : '' ?></td>
                                <td><?= h($inscricao->periodo) ?></td>
                                <td><?= $inscricao->timestamp ? $inscricao->timestamp->format('d/m/Y H:i:s') : '' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                <div class="related">
                    <h4>Sem incrições</h4>
                </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
