<?php
/**
 * @var \App\View\AppView $this
 */
?>

<!-- Picks datatables link from template -->
<?= $this->Html->css('https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css', ['block' => true]); ?>
<?= $this->Html->script('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', ['block' => true]); ?>
<?= $this->Html->script('https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', ['block' => true]); ?>

<div class="alunos cargahoraria content">
    <h3><?= __('Carga Horária') ?></h3>

    <div class='table_wrap'>
        <table id="cargahoraria" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nome</th> 
                    <th>Registro</th>
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
            <tbody>
            <?php foreach ($alunos as $aluno) : ?>
                <?php $carga_estagio = 0; ?>
                <tr>
                    <td><?php echo $this->Html->link($aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $aluno->id ]); ?></td>
                    <td><?php echo h($aluno['registro']); ?></td>
                    <td><?php echo sizeof($aluno['estagiarios']); ?></td>
                    
                    <?php for ($i = 0; $i <= 3; $i++) : ?>
                            <?php $estagiario = $aluno['estagiarios'][$i] ?? null; ?>
                            <td><?php echo $estagiario ? $estagiario['nivel'] : ''; ?></td>
                            <td><?php echo $estagiario ? $estagiario['periodo'] : '-'; ?></td>
                            <td><?php echo $estagiario ? $estagiario['ch'] : '0'; ?></td>
                            <?php $carga_estagio += $estagiario ? $estagiario['ch'] : 0; ?>
                    <?php endfor; ?>
                    <td><?php echo $carga_estagio; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
            $('#cargahoraria').DataTable(
                {
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
                    },
                    "order": [[0, 'asc']],
                    "pageLength": 20,
                }
            );
        }
    );
</script>
