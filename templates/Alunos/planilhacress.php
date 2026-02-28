<?php
// pr($cress);
// pr($periodos);
// pr($periodoselecionado);
// die();
?>

<script>
    $(document).ready(function () {
        var base_url = "<?= $this->Html->Url->build(['controller' => 'alunos', 'action' => 'planilhacress']); ?>";
        var select = $("#periodo");
		var pathname = location.pathname.split('/').filter(Boolean);
		if (pathname[pathname.length - 2] == 'planilhaseguro') select.val(pathname[pathname.length - 1]);
        
        select.on('change', function () {
            var periodo = $(this).val();
            window.location = base_url + "/" + periodo;
        })
    });

</script>

<div class='alunos planilhacress content'>
	
	<div class="row justify-content-center">
	    <div class="col-auto">
            <?= $this->Form->create(null, ['url' => 'index'], ['class' => 'form-inline']); ?>
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
        </div>
	</div>

    <h3>Planilha de estagiários para o CRESS 7ª Região</h3>

    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class='table_wrap'>
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('Alunos.nome', 'Nome') ?></th>
                    <th><?= $this->Paginator->sort('Instituicoes.instituicao', 'Instituição') ?></th>
                    <th>Endereço</th>
                    <th>CEP</th>
                    <th>Bairro</th>
                    <th><?= $this->Paginator->sort('Supervisores.nome', 'Supervisor') ?></th>
                    <th>CRESS</th>
                    <th><?= $this->Paginator->sort('Professores.nome', 'Professor') ?></th>
                </tr>
            </thead>
            <?php foreach ($cress as $c_cress): ?>
                <?php // pr($c_cress); ?>
                <tr>
                    <td><?php echo isset($c_cress->aluno->nome) ? $this->Html->link($c_cress->aluno->nome, '/alunos/view/' . $c_cress->aluno->id) : 'Sem informação'; ?></td>
                    <td><?php echo isset($c_cress->instituicao->instituicao) ? $this->Html->link($c_cress->instituicao->instituicao, '/instituicoes/view/' . $c_cress->instituicao->id) : 'Sem informação'; ?></td>
                    <td><?php echo $c_cress->instituicao->endereco; ?></td>
                    <td><?php echo isset($c_cress->instituicao->cep) ? $c_cress->instituicao->cep : ''; ?></td>
                    <td><?php echo isset($c_cress->instituicao->bairro) ? $c_cress->instituicao->bairro : ''; ?></td>
                    <td><?php echo isset($c_cress->supervisor->nome) ? $c_cress->supervisor->nome : ''; ?></td>
                    <td><?php echo isset($c_cress->supervisor->cress) ? $c_cress->supervisor->cress : ''; ?></td>
                    <td><?php echo isset($c_cress->professor->nome) ? $c_cress->professor->nome : ''; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>