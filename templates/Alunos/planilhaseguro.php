<?php 
// pr($t_seguro); 
// pr($periodos); 
// pr($periodoselecionado);
// die();
?>

<script>
    $(document).ready(function () {
        var base_url = "<?= $this->Html->Url->build(['controller' => 'Alunos', 'action' => 'planilhaseguro']); ?>";
        var select = $("#periodo");
		var pathname = location.pathname.split('/').filter(Boolean);
		if (pathname[pathname.length - 2] == 'planilhaseguro') select.val(pathname[pathname.length - 1]);

        select.on('change', function () {
            var periodo = $(this).val();
            window.location = base_url + "/" + periodo;
        })
    });
</script>

<div class="alunos planilhaseguro content">
	
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
	
    <h3>Planilha para seguro de vida dos estagiários</h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class='table_wrap'>
        <table class="table">
            <thead class='thead-light'>
            <tr>
                <th><?= $this->Paginator->sort('Alunos.nome', 'Nome') ?></th>
                <th><?= $this->Paginator->sort('Alunos.cpf', 'CPF') ?></th>
                <th><?= $this->Paginator->sort('Alunos.nascimento', 'Nascimento') ?></th>
                <th><?= $this->Paginator->sort('Alunos.registro', 'Registro') ?></th>
                <th><?= $this->Paginator->sort('curso') ?></th>
                <th><?= $this->Paginator->sort('nivel') ?></th>
                <th><?= $this->Paginator->sort('periodo') ?></th>
                <th>Início</th>
                <th>Final</th>
                <th>Instituição</th>
            </tr>
            </thead>
            <?php foreach ($seguro as $estagiario): ?>
                <?php 
                
                // pr($estagiario);
                // die();
                
                if ($estagiario->nivel == 1) {
            
                    // Início
                    $inicio = $estagiario->periodo;
            
                    // Final
                    $semestre = explode('-', $estagiario->periodo);
                    $ano = $semestre[0];
                    $indicasemestre = $semestre[1];
            
                    if ($indicasemestre == 1) {
                        $novoano = $ano + 1;
                        $novoindicasemestre = $indicasemestre + 1;
                        $final = $novoano . "-" . $novoindicasemestre;
                    } elseif ($indicasemestre == 2) {
                        $novoano = $ano + 2;
                        $final = $novoano . "-" . 1;
                    }
                    
                } elseif ($estagiario->nivel == 2) {
            
                    $semestre = explode('-', $estagiario->periodo);
                    $ano = $semestre[0];
                    $indicasemestre = $semestre[1];
            
                    // Início
                    if ($indicasemestre == 1) {
                        $novoano = $ano - 1;
                        $inicio = $novoano . "-" . 2;
                    } elseif ($indicasemestre == 2) {
                        $inicio = $ano . "-" . "1";
                    }
            
                    // Final
                    if ($indicasemestre == 1) {
                        $novoano = $ano + 1;
                        $final = $novoano . "-" . 1;
                    } elseif ($indicasemestre == 2) {
                        $novoano = $ano + 1;
                        $final = $novoano . "-" . "2";
                    }
                    
                } elseif ($estagiario->nivel == 3) {
            
                    $semestre = explode('-', $estagiario->periodo);
                    $ano = $semestre[0];
                    $indicasemestre = $semestre[1];
            
                    // Início
                    $novoano = $ano - 1;
                    $inicio = $novoano . "-" . $indicasemestre;
            
                    // Final
                    if ($indicasemestre == 1) {
                        // $ano = $ano + 1;
                        $final = $ano . "-" . 2;
                    } elseif ($indicasemestre == 2) {
                        $novoano = $ano + 1;
                        $final = $novoano . "-" . 1;
                    }
                    
                } elseif ($estagiario->nivel == 4) {
            
                    $semestre = explode('-', $estagiario->periodo);
                    $ano = $semestre[0];
                    $indicasemestre = $semestre[1];
            
                    // Início
                    if ($indicasemestre == 1) {
                        $ano = $ano - 2;
                        $inicio = $ano . "-" . 2;
                    } elseif ($indicasemestre == 2) {
                        $ano = $ano - 1;
                        $inicio = $ano . "-" . 1;
                    }
            
                    // Final
                    $final = $estagiario->periodo;
            
                    // Estagio não obrigatório. Conto como estágio 5
                    
                } elseif ($estagiario->nivel == 9) {
            
                    $semestre = explode('-', $estagiario->periodo);
                    $ano = $semestre[0];
                    $indicasemestre = $semestre[1];
            
                    // Início
                    if ($indicasemestre == 1) {
                        $ano = $ano - 2;
                        $inicio = $ano . "-" . 1;
                    } elseif ($indicasemestre == 2) {
                        $ano = $ano - 2;
                        $inicio = $ano . "-" . 2;
                    }
            
                    // Final
                    $final = $estagiario->periodo;
                }
                
                $estagiario->curso = $instituicao;
            
                
                if ($estagiario->nivel == 9):
                    $c_seguro['nivel'] = "Não obrigatório";
                endif;
                
                $estagiario['inicio'] = $inicio;
                $estagiario['final'] = $final;
                
                ?>
                <tr>
                    <td>
                        <?php echo $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome , ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id ]) : ''; ?>
                    </td>
                    <td>
                        <?php echo $estagiario->aluno->cpf; ?>
                    </td>
                    <td>
                        <?php if (empty($estagiario->aluno->nascimento)): ?>
                            <?php echo "s/d"; ?>
                        <?php else: ?>
                            <?php echo date('d-m-Y', strtotime($estagiario->aluno->nascimento)); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $estagiario->aluno->registro; ?>
                    </td>
                    <td>
                        <?php echo $estagiario->curso; ?>
                    </td>
                    <td>
                        <?php echo $estagiario->nivel; ?>
                    </td>
                    <td>
                        <?php echo $estagiario->periodo; ?>
                    </td>
                    <td>
                        <?php echo $estagiario['inicio']; ?>
                    </td>
                    <td>
                        <?php echo $estagiario['final']; ?>
                    </td>
                    <td>
                        <?php echo ($estagiario->instituicao) ? $this->Html->link($estagiario->instituicao->instituicao , ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id ]) : ''; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>