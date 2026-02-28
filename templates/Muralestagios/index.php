<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muralestagio[]|\Cake\Collection\CollectionInterface $muralestagios
 */
declare(strict_types=1);


$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<script type="text/javascript">
	$(document).ready(function () {
        var base_url = "<?= $this->Html->Url->build(['controller' => 'muralestagios']); ?>";
        var select = $("#periodo");
		var pathname = location.pathname.split('/').filter(Boolean);
		if (pathname[pathname.length - 2] == 'index') select.val(pathname[pathname.length - 1]);
		select.on('change', function () {
            var periodo = $(this).val();
            window.location = base_url + '/index/' + periodo;
        });
    });
</script>

<div class="muralestagios index content">
	
	<div class="row justify-content-center">
	    <div class="col-auto">
	        <?php if ($user_data['administrador_id']): ?>
	            <?= $this->Form->create($muralestagios, ['class' => 'form-inline']); ?>
					<?= $this->Form->label('periodo', 'Período'); ?>
					<?= $this->Form->input('periodo', [
							'default'=> $periodo ? $periodo : $configuracao['mural_periodo_atual'],
							'id' => 'periodo', 
							'type' => 'select', 
							'options' => $periodos,
							'class' => 'form-control'
						]); 
					?>
	            <?= $this->Form->end(); ?>
	        <?php else: ?>
	            <h2 class="label">Período: <?= $configuracao['mural_periodo_atual']; ?></h2>
	        <?php endif; ?>
	    </div>
	</div>
	
	<aside>
		<div class="nav">
	        <?php if ($user_data['administrador_id'] || $user_data['professor_id'] || $user_data['supervisor_id']): ?>
			    <?= $this->Html->link(__('Novas vagas'), ['action' => 'add'], ['class' => 'button']) ?>
	        <?php endif; ?>
		</div>
	</aside>
	
	<h3><?= __('Mural de estágios') ?></h3>
	
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
                    <th><?= $this->Paginator->sort('Instituicao.instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('vagas') ?></th>
                    <th><?= $this->Paginator->sort('beneficios') ?></th>
                    <th><?= $this->Paginator->sort('fim_de_semana', 'Fim de semana') ?></th>
                    <th><?= $this->Paginator->sort('carga_horaria', 'Carga Horária') ?></th>
                    <th><?= $this->Paginator->sort('data_selecao', 'Seleção') ?></th>
                    <th><?= $this->Paginator->sort('data_inscricao', 'Inscrição') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($muralestagios as $muralestagio): ?>
                    <tr>
				        <?php if ($user_data['administrador_id']): ?>
	                        <td class="actions">
	                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $muralestagio->id]) ?>
	                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $muralestagio->id]) ?>
	                            <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $muralestagio->id], ['confirm' => __('Are you sure you want to delete muralestagio_{0}?', $muralestagio->id)]) ?>
	                        </td>
	                        <td><?= $this->Html->link((string)$muralestagio->id, ['action' => 'view', $muralestagio->id]) ?></td>
				        <?php endif; ?>
                        <td><?= $muralestagio->instituicao ? $this->Html->link($muralestagio->instituicao->instituicao, ['controller' => 'Muralestagios', 'action' => 'view', $muralestagio->id]) : '' ?></td>
                        <td><?= h($muralestagio->vagas) ?></td>
                        <td><?= h($muralestagio->beneficios) ?></td>
                        <td>
							<?php
							$fim_de_semana = '';
							switch ( $muralestagio->fim_de_semana ) {
								case 0: $fim_de_semana = 'Não';          break;
								case 1: $fim_de_semana = 'Sim';          break;
								case 2: $fim_de_semana = 'Parcialmente'; break;
							}
							echo $fim_de_semana;
							?>
						</td>
                        <td><?= h($muralestagio->carga_horaria) ?></td>
                        <td><?= h($muralestagio->data_selecao) ?></td>
                        <td><?= h($muralestagio->data_inscricao) ?></td>
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
