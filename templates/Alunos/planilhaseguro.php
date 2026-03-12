<?php 
declare(strict_types=1);
use Cake\I18n\Date;
?>

<script type="text/javascript">
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
                
                $ajuste2020 = (int)$estagiario->ajuste2020;
                $semestre = explode('-', $estagiario->periodo);
                $ano = (int)$semestre[0];
                $indicasemestre = (int)$semestre[1];

                if ($estagiario->nivel == 1) {
            
                    // Início
                    $inicio = $estagiario->periodo;
            
                    // Final
                    if ($ajuste2020 == 1) {
                        // Needs 3 semesters: 1st, 2nd, 3rd.
                        // If 1-1 -> 2-1
                        // If 1-2 -> 2-2
                        $novoano = $ano + 1;
                        $final = $novoano . "-" . $indicasemestre;
                    } else {
                        // Needs 4 semesters: 1st, 2nd, 3rd, 4th.
                        // If 1-1 -> 2-2
                        // If 1-2 -> 3-1
                        if ($indicasemestre == 1) {
                            $novoano = $ano + 1;
                            $final = $novoano . "-" . 2;
                        } else {
                            $novoano = $ano + 2;
                            $final = $novoano . "-" . 1;
                        }
                    }
                    
                } elseif ($estagiario->nivel == 2) {
            
                    // Início
                    // Level 2 is always 2nd semester.
                    // If 1-1 -> 1-1
                    // If 1-2 -> 1-2
                    if ($indicasemestre == 1) {
                        $inicio = $ano . "-" . 1;
                    } else {
                        $inicio = $ano . "-" . 2;
                    }
            
                    // Final
                    if ($ajuste2020 == 1) {
                        // Ends at level 3.
                        // If 2-1 -> 2-2
                        // If 2-2 -> 3-1
                        if ($indicasemestre == 1) {
                            $final = $ano . "-" . 2;
                        } else {
                            $novoano = $ano + 1;
                            $final = $novoano . "-" . 1;
                        }
                    } else {
                        // Ends at level 4.
                        // If 2-1 -> 3-1
                        // If 2-2 -> 3-2
                        $novoano = $ano + 1;
                        $final = $novoano . "-" . $indicasemestre;
                    }
                    
                } elseif ($estagiario->nivel == 3) {
            
                    // Início
                    // Level 3 is always 3rd semester.
                    if ($ajuste2020 == 1) {
                        // Start was level 1 (2 semesters ago)
                        // If 3-1 -> 2-1
                        // If 3-2 -> 2-2
                        $novoano = $ano - 1;
                        $inicio = $novoano . "-" . $indicasemestre;
                    } else {
                        // Start was level 1 (2 semesters ago)
                        // If 3-1 -> 2-1 (Wait, level 1 was 2 semesters before)
                        // If 3-1 -> 2-1
                        // If 3-2 -> 2-2
                        $novoano = $ano - 1;
                        $inicio = $novoano . "-" . $indicasemestre;
                    }
            
                    // Final
                    if ($ajuste2020 == 1) {
                        // Ends now (level 3)
                        $final = $estagiario->periodo;
                    } else {
                        // Ends at level 4.
                        // If 3-1 -> 3-2
                        // If 3-2 -> 4-1
                        if ($indicasemestre == 1) {
                            $final = $ano . "-" . 2;
                        } else {
                            $novoano = $ano + 1;
                            $final = $novoano . "-" . 1;
                        }
                    }
                    
                } elseif ($estagiario->nivel == 4) {
            
                    // Início
                    // Level 4 is always 4th semester.
                    // Start was level 1 (3 semesters ago)
                    // If 4-1 -> 2-2
                    // If 4-2 -> 3-1
                    if ($indicasemestre == 1) {
                        $novoano = $ano - 2;
                        $inicio = $novoano . "-" . 2;
                    } else {
                        $novoano = $ano - 1;
                        $inicio = $novoano . "-" . 1;
                    }
            
                    // Final
                    $final = $estagiario->periodo;
            
                } elseif ($estagiario->nivel == 9) {
            
                    // Início
                    // Assumed as level 5 or 4? The user said 4 or 3 semesters.
                    // If we assume it as a "long" one or just use original logic:
                    if ($indicasemestre == 1) {
                        $novoano = $ano - 2;
                        $inicio = $novoano . "-" . 1;
                    } else {
                        $novoano = $ano - 2;
                        $inicio = $novoano . "-" . 2;
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
                            <?php echo $estagiario->aluno->nascimento->format('d-m-Y'); ?>
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