<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor[]|\Cake\Collection\CollectionInterface $professores
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
    'Política social' => 'Politicas'
]
?>

<div class="professores index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Novo Professor'), ['action' => 'add'], ['class' => 'button']) ?>
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
                    <th><?= $this->Paginator->sort('curriculolattes') ?></th>
                    <th><?= $this->Paginator->sort('departamento') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($professores as $professor): ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $professor->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $professor->id]) ?>
                        <?php if ($user_data['categoria'] == '1'): ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $professor->id], ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome)]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Html->link((string)$professor->id, ['action' => 'view', $professor->id]) ?></td>
                    <td><?= $this->Html->link(h($professor->nome), ['action' => 'view', $professor->id]) ?></td>
                    <td><?= (string)$professor->siape ? $professor->siape : 'S/d' ?></td>
                    <td><?= $professor->celular ? '(' . h($professor->ddd_celular) . ')' . h($professor->celular) : '' ?></td>
                    <td><?= ($professor->email) ? $this->Text->autoLinkEmails($professor->email) : '' ?></td>
                    <td><?= $professor->curriculolattes ? $this->Html->link('http://lattes.cnpq.br/' . h($professor->curriculolattes)) : '' ?></td>
                    <td><?= h($professor->departamento) ?></td>
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
