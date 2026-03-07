<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao[]|\Cake\Collection\CollectionInterface $instituicoes
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
		<div class="nav">    
        	<?php if ($user_data['categoria'] == '1'): ?>
                <?= $this->Html->link(__('Nova Instituição'), ['action' => 'add'], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Áreas'), ['controller' => 'Areas', 'action' => 'index'], ['class' => 'button']) ?>
            <?php endif; ?>
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
				    <?php if ($user_data['administrador_id']): ?>
                        <th class="actions"><?= __('Actions') ?></th>
                        <th><?= $this->Paginator->sort('id') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('Area.area', 'Área') ?></th>
                    <th><?= $this->Paginator->sort('natureza') ?></th>
                    <th><?= $this->Paginator->sort('cnpj', 'CNPJ') ?></th>
                    <th><?= $this->Paginator->sort('convenio', 'Convênio') ?></th>
                    <th><?= $this->Paginator->sort('expira', 'Expira') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instituicoes as $instituicao): ?>
                <tr>
				    <?php if ($user_data['administrador_id']): ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $instituicao->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $instituicao->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $instituicao->id)]) ?>
                        </td>
                        <td><?= $this->Html->link((string)$instituicao->id, ['action' => 'view', $instituicao->id]) ?></td>                    
                    <?php endif; ?>
                    <td><?= $this->Html->link($instituicao->instituicao, ['controller' => 'instituicoes', 'action' => 'view', $instituicao->id]) ?></td>
                    <td><?= $instituicao->area_rel ? $this->Html->link($instituicao->area_rel->area, ['controller' => 'Areas', 'action' => 'view', $instituicao->area_rel->id]) : '' ?></td>
                    <td><?= h($instituicao->natureza) ?></td>
                    <td><?= h($instituicao->cnpj) ?></td>
                    <td><?= h($instituicao->convenio) ?></td>
                    <td><?= h($instituicao->expira) ?></td>
                    <td><?= $instituicao->email ? $this->Text->autoLinkEmails($instituicao->email) : '' ?></td>

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
