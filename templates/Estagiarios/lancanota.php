<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario[]|\Cake\Collection\CollectionInterface $estagiarios
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?= $this->Html->Url->build(['controller' => 'estagiarios', 'action' => 'lancanota']); ?>";
        var select = $("#periodo-select");
        select.change(function () {
            var periodo = $(this).val();
            var professor_id = "<?= $professor->id; ?>";
            window.location = url + '?periodo=' + periodo + '&professor_id=' + professor_id;
        });
    });
</script>

<div class="row justify-content-between mb-3">
    <div class="col-auto">
        <h1 class="h3 mb-0 text-gray-800">Alunos estagiários: <?= h($professor->nome); ?></h1>
    </div>
    <div class="col-auto">
        <?= $this->Html->link(__('Imprimir'), ['action' => 'lancanotapdf', '?' => ['periodo' => $periodo, 'professor_id' => $professor->id]], ['class' => 'button']) ?>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'form-inline']); ?>
        <div class="form-group mb-2">
            <label for="periodo-select" class="mr-2">Período: </label>
            <?= $this->Form->select('periodo', $periodos, [
                'val' => $periodo,
                'id' => 'periodo-select',
                'class' => 'form-control custom-select',
                'empty' => false
            ]); ?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>

<div class="container">
    <h3><?= __('Estagiários') ?></h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-responsive" id="table-estagiarios">
            <thead>
                <tr id="table-estagiarios-header">
                    <?php if ($user_data['categoria'] == '1' || $user_data['categoria'] == '3'): ?>
                        <th><?= $this->Paginator->sort('Estagiarios.id', 'Id') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('Alunos.nome', 'Aluno(a)') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.registro', 'Registro') ?></th>
                    <th><?= $this->Paginator->sort('Instituicoes.instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('Supervisores.nome', 'Supervisor(a)') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.periodo', 'Período') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.nivel', 'Nível') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.nota', 'Nota') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.ch', 'CH') ?></th>
                    <th><?= __('Atividades') ?></th>
                    <th><?= __('Avaliação discente') ?></th>
                    <th class="actions"><?= __('Ações') ?></th>
                </tr>
            </thead>
            <tbody id="table-estagiarios-body">
                <?php foreach ($estagiarios as $estagiario): ?>
                    <?php // pr($estagiario); die(); ?>
                    <tr data-id="<?= $estagiario->id ?>">
                        <?php if ($user_data['categoria'] == '1' || $user_data['categoria'] == '3'): ?>
                            <td><?= $estagiario->id ?></td>
                        <?php endif; ?>
                        <td><?= $this->Html->link($estagiario->aluno->nome ?? 'S/d', ['controller' => 'Alunos', 'action' => 'view', $estagiario['aluno_id']]) ?>
                        </td>
                        <td><?= $estagiario['registro'] ?></td>
                        <td>
                            <?php if (isset($estagiario['instituicao_id'])): ?>
                                <?= $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario['instituicao_id']]) ?>
                            <?php else: ?>
                               <?= "Sem instituicao"; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($estagiario['supervisor_id'])) {
                                echo $this->Html->link($estagiario->supervisor->nome ?? 'S/d', ['controller' => 'Supervisores', 'action' => 'view', $estagiario['supervisor_id']]); 
                            } else { 
                                echo "Sem supervisor";
                            }
                            ?>
                        </td>
                        <td><?= $estagiario['periodo'] ?></td>
                        <td><?= $estagiario['nivel'] ?></td>
                        <td class="editable-field" data-field="nota"><?= $this->Number->format($estagiario['nota'], ['precision' => 2]) ?></td>
                        <td class="editable-field" data-field="ch"><?= $this->Number->format($estagiario['ch']) ?></td>
                        <?php if (isset($estagiario['folha_id'])): ?>
                            <td><?= $this->Html->link('Folha de atividades', ['controller' => 'Folhadeatividades', 'action' => 'index', $estagiario['id']]) ?>
                            </td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                        <?php if (isset($estagiario['avaliacao_id'])): ?>
                            <td><?= $this->Html->link('Ver avaliação', ['controller' => 'avaliacoes', 'action' => 'view', $estagiario['avaliacao_id']]) ?>
                            </td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $estagiario['id']]) ?>
                            <?php if ($user_data['categoria'] == '1' || $user_data['categoria'] == '3' && ($user_data['professor_id'] == $estagiario->professor_id)): ?>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"><?= __('Editar') ?></button>
                                <button type="button" class="btn btn-sm btn-primary btn-save" style="display:none">Salvar</button>
                                <button type="button" class="btn btn-sm btn-secondary btn-cancel" style="display:none">Cancelar</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#table-estagiarios tbody');
    if (!tableBody) return;

    tableBody.addEventListener('click', (event) => {
        const target = event.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.classList.contains('btn-edit')) {
            makeRowEditable(row);
        } else if (target.classList.contains('btn-save')) {
            saveRow(row);
        } else if (target.classList.contains('btn-cancel')) {
            cancelEdit(row);
        }
    });
});

function makeRowEditable(row) {
    row.classList.add('editing');
    const cells = row.querySelectorAll('.editable-field');
    cells.forEach(cell => {
        const text = cell.textContent.trim() === '' ? '' : cell.textContent.trim();
        cell.innerHTML = `<input class="form-control form-control-sm" type="text" value="${text}">`;
    });

    // Toggle buttons
    row.querySelector('.btn-edit').style.display = 'none';
    row.querySelector('.btn-save').style.display = 'inline-block';
    row.querySelector('.btn-cancel').style.display = 'inline-block';

}

function saveRow(row) {
    const cells = row.querySelectorAll('.editable-field');
    const data = {
        id: row.dataset.id
    };
    cells.forEach(cell => {
        const input = cell.querySelector('input');
        const fieldName = cell.dataset.field;
        let value = input.value.trim();
        cell.textContent = value;
        data[fieldName] = value;
    });
 
    $.ajax({
        url: '<?= $this->Url->build(['controller' => 'Estagiarios', 'action' => 'edit']) ?>',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/x-www-form-urlencoded',
        headers: {
            'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>',
            'Accept': 'application/json'
        },
        data: $.param(data),
        beforeSend: function() {
        },
        complete: function() {
            console.log('Data sent:', data);
        },
        success: function(response) {
            console.log('Success:', response);
            if (response.status === 'success') {
                // Add a brief success indicator
                const saveBtn = row.querySelector('.btn-save');
                saveBtn.textContent = 'Salvo!';
                saveBtn.classList.remove('btn-primary');
                saveBtn.classList.add('btn-success');
                
                setTimeout(() => {
                    row.classList.remove('editing');
                    row.querySelector('.btn-edit').style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    saveBtn.textContent = 'Salvar';
                    saveBtn.classList.remove('btn-success');
                    saveBtn.classList.add('btn-primary');
                    row.querySelector('.btn-cancel').style.display = 'none';
                }, 1000);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error details:', xhr.responseText);
            // console.error('Error:', error);
            alert('Erro ao salvar as alterações. Verifique o console para mais detalhes.');
            // Revert state if needed or keep editable
        }
    });
}

function cancelEdit(row) {
    row.classList.remove('editing');
    const cells = row.querySelectorAll('.editable-field');
    cells.forEach(cell => {
        cell.textContent = cell.textContent.trim() === '' ? '' : cell.textContent.trim();
    });
}

</script>    
