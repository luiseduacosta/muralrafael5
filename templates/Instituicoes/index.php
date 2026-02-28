<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao[]|\Cake\Collection\CollectionInterface $instituicoes
 */
declare(strict_types=1);


$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div class="instituicoes index content">
	<aside>
		<div class="nav">    
        	<?php if ($user_data['administrador_id']): ?>
                <?= $this->Html->link(__('Nova Instituição'), ['action' => 'add'], ['class' => 'button']) ?>
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
                    <th><?= $this->Paginator->sort('instituicao') ?></th>
                    <th><?= $this->Paginator->sort('Area.area', 'Área') ?></th>
                    <th><?= $this->Paginator->sort('natureza') ?></th>
                    <th><?= $this->Paginator->sort('cnpj') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('url') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instituicoes as $instituicao): ?>
                <tr>
				    <?php if ($user_data['administrador_id']): ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $instituicao->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $instituicao->id]) ?>
                            <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $instituicao->id)]) ?>
                        </td>
                        <td><?= $this->Html->link((string)$instituicao->id, ['action' => 'view', $instituicao->id]) ?></td>                    
                    <?php endif; ?>
                    <td><?= $this->Html->link($instituicao->instituicao, ['controller' => 'instituicoes', 'action' => 'view', $instituicao->id]) ?></td>
                    <td><?= $instituicao->area ? $this->Html->link($instituicao->area->area, ['controller' => 'Areas', 'action' => 'view', $instituicao->area->id]) : '' ?></td>
                    <td><?= h($instituicao->natureza) ?></td>
                    <td><?= h($instituicao->cnpj) ?></td>
                    <td><?= $instituicao->email ? $this->Text->autoLinkEmails($instituicao->email) : '' ?></td>
                    <td><?= $instituicao->url ? $this->Html->link($instituicao->url) : '' ?></td>
                    <td><?= h($instituicao->avaliacao) ?></td>
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
