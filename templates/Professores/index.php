<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Professor> $professores
 */
?>

<?php
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

// May be this is a temporary solution. Put into de Configuracoes table in json data format is a better solution
$departamentos = [
    'Fundamentos' => 'Fundamentos',
    'Métodos e técnicas' => 'Metodologia',
    'Política social' => 'Politicas',
]
?>

<div class="professores index content">
    <aside>
        <div class="row">
            <div class="col-sm-6 d-flex justify-content-start">
                <?= $this->Html->link(__('Novo(a) Professor(a)'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'class' => 'form-inline']) ?>
                    <div class="form-group">
                        <?= $this->Form->label('busca', 'Busca', ['class' => 'button mr-2 mb-4']) ?>
                        <?= $this->Form->control('busca', ['placeholder' => 'Busca professor(a)', 'label' => false, 'onKeyDown' => 'if (event.keyCode == 13) {this.form.submit();}', 'class' => 'form-control']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </aside>
    
    <h3><?= __('Lista de Professores(as)') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('Professores.nome', 'Nome') ?></th>
                    <th><?= $this->Paginator->sort('siape', 'SIAPE') ?></th>
                    <th><?= $this->Paginator->sort('celular') ?></th>
                    <th><?= $this->Paginator->sort('email', 'Email') ?></th>
                    <th><?= $this->Paginator->sort('curriculolattes', 'Lattes') ?></th>
                    <th><?= $this->Paginator->sort('departamento') ?></th>
                    <th><?= $this->Paginator->sort('motivoegresso', 'Egresso') ?></th>
                    <th><?= $this->Paginator->sort('estagiarios_count', 'Estagiarios') ?></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($professores as $professor) : ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $professor->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $professor->id]) ?>
                        <?php if ($user_data['administrador_id']) : ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $professor->id], ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome)]) ?>
                        <?php endif; ?>
                     </td>
                    <td><?= $this->Html->link((string)$professor->id, ['action' => 'view', $professor->id]) ?></td>
                    <td><?= $this->Html->link(h($professor->nome), ['action' => 'view', $professor->id]) ?></td>
                    <td><?= (string)$professor->siape ? $professor->siape : 'S/d' ?></td>

                    <td>
                        <?php if (!empty($professor->celular) && strlen($professor->celular) < 10) : ?>
                            <?= '(' . h($professor->ddd_celular) . ') ' . h($professor->celular) ?>
                        <?php else: ?>
                            <?= $professor->celular ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $professor->email ? $this->Text->autoLinkEmails($professor->email) : '' ?></td>
                    <td><?= $professor->curriculolattes ? $this->Html->link('http://lattes.cnpq.br/' . h($professor->curriculolattes), ['target' => '_blank']) : '' ?></td>
                    <td><?= h($departamentos[$professor->departamento] ?? $professor->departamento) ?></td>
                    <td><?= h($professor->motivoegresso) ?></td>
                    <td><?= h($professor->estagiarios_count) ?></td>
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
