<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Instituicao> $instituicoes
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<div class="instituicoes index content">
    <aside>
        <div class="row">
            <div class="col-6">
                <?php if ($user_data['administrador_id']) : ?>
                    <?= $this->Html->link(__('Nova Instituição'), ['action' => 'add'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Áreas'), ['controller' => 'Areas', 'action' => 'index'], ['class' => 'button']) ?>
                <?php endif; ?>
            </div>
            <div class="col-6 d-flex justify-content-end">
                <div class="search-form">
                    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'class' => 'form-inline']) ?>
                        <div class="form-group">
                            <?= $this->Form->label('busca', 'Busca', ['class' => 'button mr-2 mb-4']) ?>
                            <?= $this->Form->control('busca', ['placeholder' => 'Busca instituição', 'label' => false, 'onKeyDown' => 'if (event.keyCode == 13) {this.form.submit();}', 'class' => 'form-control']) ?>
                        </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </aside>
    
    <h3><?= __('Lista de Instituições') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <?php if ($user_data['administrador_id']) : ?>
                        <th class="actions"><?= __('Actions') ?></th>
                        <th><?= $this->Paginator->sort('id') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('Instituicoes.instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('cnpj', 'CNPJ') ?></th>
                    <th><?= $this->Paginator->sort('convenio', 'Nº de convênio') ?></th>
                    <th><?= $this->Paginator->sort('expira', 'Expira') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('estagiarios_count', 'Estagiarios') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instituicoes as $instituicao) : ?>
                <tr>
                    <?php if ($user_data['administrador_id']) : ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $instituicao->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $instituicao->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $instituicao->id)]) ?>
                        </td>
                        <td><?= $this->Html->link((string)$instituicao->id, ['action' => 'view', $instituicao->id]) ?></td>                    
                    <?php endif; ?>
                    <td><?= $this->Html->link($instituicao->instituicao, ['controller' => 'instituicoes', 'action' => 'view', $instituicao->id]) ?></td>
                    <td><?= h($instituicao->cnpj) ?></td>
                    <td><?= !empty($instituicao->convenio) ? $instituicao->convenio : '' ?></td>
                    <td><?= !empty($instituicao->expira) ? $instituicao->expira->format('d/m/Y') : '' ?></td>
                    <td><?= !empty($instituicao->email) ? $this->Text->autoLinkEmails($instituicao->email) : '' ?></td>
                    <td><?= h($instituicao->estagiarios_count) ?></td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>
