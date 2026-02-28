<?php
/**
 * @var \App\View\AppView $this
 */

//pr($carga_horaria_total); 
?>
<div class="alunos cargahoraria content">
    <h3><?= __('Carga Horária') ?></h3>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class='table_wrap'>
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th> 
                    <th><?= $this->Paginator->sort('registro') ?></th>
                    <th>Semestres</th>
                    <th>Nível</th>
                    <th>Período</th>
                    <th>CH 1</th>
                    <th>Nível</th>
                    <th>Período</th>
                    <th>CH 2</th>
                    <th>Nível</th>
                    <th>Período</th>
                    <th>CH 3</th>
                    <th>Nível</th>
                    <th>Período</th>
                    <th>CH 4</th>
                    <th>Total</th>
                </tr>
            </thead>

            <?php foreach ($alunos as $aluno): ?>
            <?php //pr($aluno['estagiarios']); ?>
            <?php $carga_estagio = 0; ?>
                <tr>
                    <td><?php echo $this->Html->link($aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $aluno->id ]); ?></td>
                    <td><?php echo h($aluno['registro']); ?></td>
                    <td><?php echo sizeof($aluno['estagiarios']); ?></td>
                    
                    <?php for ($i = 0; $i <= 3; $i++): ?>
                            <?php $estagiario = $aluno['estagiarios'][$i] ?? null; ?>
                            <td><?php echo $estagiario ? $estagiario['nivel'] : ''; ?></td>
                            <td><?php echo $estagiario ? $estagiario['periodo'] : '-'; ?></td>
                            <td><?php echo $estagiario ? $estagiario['ch'] : '0'; ?></td>
                            <?php $carga_estagio += $estagiario ? $estagiario['ch'] : 0; ?>
                    <?php endfor; ?>
                    <td><?php echo $carga_estagio; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>