<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao[]|\Cake\Collection\CollectionInterface $inscricoes
 */
// pr($inscricoes);
// die();
?>
<div class="inscricoes index content">
	<aside>
		<div class="nav">
            <?= $this->Html->link(__('Nova Inscricao'), ['action' => 'add'], ['class' => 'button']) ?>
		</div>
	</aside>
    
    <h3><?= __('Lista de inscrições') ?></h3>
	
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('aluno_id') ?></th>
                    <th><?= $this->Paginator->sort('registro') ?></th>
                    <th><?= $this->Paginator->sort('muralestagio_id') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th><?= $this->Paginator->sort('periodo') ?></th>
                    <th><?= $this->Paginator->sort('timestamp') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscricoes as $inscricao): ?>

    
<?php
//pr($inscricao);
//die();
?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $inscricao->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $inscricao->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $inscricao->id)]) ?>
                    </td>
                    <td><?= $this->Html->link((string)$inscricao->id, ['action' => 'view', $inscricao->id]) ?></td>
                    <td><?= $inscricao->aluno ? $this->Html->link($inscricao->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $inscricao->aluno->id]) : '' ?></td>
                    <td><?= $this->Number->format($inscricao->registro) ?></td>
					<td><?= $inscricao->muralestagio ? $this->Html->link($inscricao->muralestagio->instituicao ? $inscricao->muralestagio->instituicao->instituicao . ' (' . $inscricao->muralestagio->dataSelecao . ')' : $inscricao->muralestagio->id , ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : $inscricao->muralestagio_id ?></td>
                    <td><?= h($inscricao->data) ?></td>
                    <td><?= h($inscricao->periodo) ?></td>
                    <td><?= $inscricao->timestamp ? h($inscricao->timestamp) : '' ?></td>
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
