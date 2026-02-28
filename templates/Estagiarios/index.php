<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario[]|\Cake\Collection\CollectionInterface $estagiarios
 */

declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?= $this->Html->Url->build(['controller' => 'estagiarios']); ?>";
        var select= $("#estagiarioperiodo");
		var pathname = location.pathname.split('/').filter(Boolean);
		if (pathname[pathname.length - 2] == 'index') select.val(pathname[pathname.length - 1]);
        select.change(function () {
            var periodo = $(this).val();
            window.location = url + '/index/' + periodo;
        });
    });
</script>

<div class="estagiarios index content">
	
	<div class="row justify-content-center">
	    <div class="col-auto">
	        <?php if ($user_data['administrador_id']): ?>
	            <?= $this->Form->create($estagiarios, ['class' => 'form-inline']); ?>
					<?= $this->Form->label('estagiarioperiodo', 'Período'); ?>
					<?= $this->Form->input('periodo', [
							'default'=> $periodo ? $periodo : $configuracao['mural_periodo_atual'],
							'id' => 'estagiarioperiodo', 
							'type' => 'select', 
							'options' => $periodos, 
							'class' => 'form-control'
						]); 
					?>
	            <?= $this->Form->end(); ?>
	        <?php else: ?>
	            <h3 class="label">Período: <?= $configuracao['mural_periodo_atual']; ?></h3>
	        <?php endif; ?>
	    </div>
	</div>
	
	<aside>
		<div class="nav">
	        <?php if ($user_data['administrador_id']): ?>
			    <?= $this->Html->link(__('Novo Estagiario'), ['action' => 'add'], ['class' => 'button']) ?>
	        <?php endif; ?>
		</div>
	</aside>
	
    <h3><?= __('Lista de Estagiarios') ?></h3>
	
	<div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
			        <?php if ($user_data['administrador_id']): ?>
	                    <th class="actions"><?= __('Actions') ?></th>
					<?php endif; ?>
	                <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('Alunos.nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('Instituicoes.instituicao', 'Instituicao') ?></th>
                    <th><?= $this->Paginator->sort('periodo') ?></th>
                    <th><?= $this->Paginator->sort('turno') ?></th>
                    <th><?= $this->Paginator->sort('nivel') ?></th>
                    <th><?= $this->Paginator->sort('Supervisores.nome', 'Supervisor') ?></th>
                    <th><?= $this->Paginator->sort('Professores.nome', 'Professor') ?></th>
                    <th><?= $this->Paginator->sort('Turmas.turma', 'Turma') ?></th>
                    <th><?= $this->Paginator->sort('nota') ?></th>
                    <th><?= $this->Paginator->sort('ch') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estagiarios as $estagiario): ?>
                <tr>
					<?php if ($user_data['administrador_id']): ?>
	                    <td class="actions">
	                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $estagiario->id]) ?>
	                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $estagiario->id]) ?>
	                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id)]) ?>
	                    </td>
                    <?php endif; ?>
	                <td><?= $this->Html->link((string)$estagiario->id, ['action' => 'view', $estagiario->id]) ?></td>
					<td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                    <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
					<td><?= h($estagiario->periodo) ?></td>
                    <td><?= $estagiario->turno ? h($estagiario->turno->turno) : '' ?></td>
                    <td><?= h($estagiario->nivel) ?></td>
                    <td><?= ($estagiario->supervisor and $estagiario->supervisor->nome) ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                    <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                    <td><?= $estagiario->turma ? $this->Html->link($estagiario->turma->turma, ['controller' => 'Turmas', 'action' => 'view', $estagiario->turma->id]) : '' ?></td>
                    <td><?= $estagiario->nota ? $this->Number->format($estagiario->nota) : '' ?></td>
                    <td><?= $estagiario->ch ? $this->Number->format($estagiario->ch) : '' ?></td>
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
