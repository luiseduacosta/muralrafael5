<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */
declare(strict_types=1);

use Cake\Utility\Inflector;
use Cake\I18n\Date;

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<script type="text/javascript">

    function getsupervisores(id) {
        // Se nenhuma instituição estiver selecionada, apenas redefine o campo de supervisor
        if (!id) {
            $('#supervisor-id').html('<option value=""><?= __('Selecione o supervisor') ?></option>');
            return;
        }

        $.ajax({
            url: '<?= $this->Url->build(['controller' => 'Instituicoes', 'action' => 'selecionasupervisores']) ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                _csrfToken: '<?= $this->request->getAttribute('csrfToken') ?>'
            },
            success: function (response) {
                let options = '<option value=""><?= __('Selecione o supervisor') ?></option>';
                if (response && Object.keys(response).length > 0) {
                    $.each(response, function (key, value) {
                        options += '<option value="' + key + '">' + value + '</option>';
                    });
                } else {
                    options = '<option value=""><?= __('Nenhum supervisor encontrado') ?></option>';
                }
                $('#supervisor-id').html(options);
            },
            error: function (xhr, status, error) {
                console.error('Ajax error:', error);
                $('#supervisor-id').html('<option value=""><?= __('Erro ao carregar supervisores') ?></option>');
            }
        });
    }
    $(document).ready(function () {
        $('#instituicao-id').change(function () {
            console.log('Instituicao ID changed:', $(this).val());
            getsupervisores($(this).val());
        });
    });
</script>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<!-- Needs this style to show the icons -->
<style>
    .editor-toolbar button {
        color: #333 !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    $(document).ready(function () {
        const easyMDE = new EasyMDE({element: document.getElementById('observacoes')});
    });
</script>

<div>
    <div class="column-responsive column-80">
        <div class="estagiarios form content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['administrador_id']): ?>
                        <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php elseif ($user_data['aluno_id'] == $estagiario->aluno_id): ?>
                        <?= $this->Html->link(__('Ver aluno'), ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno_id], ['class' => 'button']) ?>
                    <?php endif; ?>
                    <?php if ($user_data['categoria'] == "1"): ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $estagiario->id],
                            ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id), 'class' => 'button']
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>

            <?= $this->Form->create($estagiario) ?>
            <fieldset>
                <h3><?= __('Editando estagiario_' . $estagiario->id) ?></h3>
                <?php
                    echo $this->Form->control('aluno_id', ['options' => $alunos, 'class' => 'form-control']);
                    echo $this->Form->control('registro', ['required' => true]);
                    echo $this->Form->control('nivel', ['required' => true]);
                    echo $this->Form->control('ajuste2020', ['options' => ['1' => 'Sim (3 semestres)', '0' => 'Não (4 semestres)']]);
                    echo $this->Form->control('tc', ['label' => 'Termo de compromisso assinado S/N?', 'options' => ['1' => 'Sim', '0' => 'Nao']]);
                    echo $this->Form->control('tc_solicitacao', ['label' => 'Data de solicitacao do termo de compromisso', 'required' => false]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'required' => true, 'empty'=> true, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'required' => true, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'required' => false, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['label' => 'Periodo', 'required' => true]);
                    echo $this->Form->control('benetransporte', ['label' => 'Beneficio de transporte', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'required' => false]);
                    echo $this->Form->control('benealimentacao', ['label' => 'Beneficio de alimentacao', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'required' => false]);
                    echo $this->Form->control('benbolsa', ['label' => 'Beneficio de bolsa - valor em R$', 'required' => false, 'type' => 'text']);
                    if ($user_data['categoria'] == "1" || $user_data['categoria'] == "3" && ($user_data['professor_id'] == $estagiario->professor_id)) {
                        echo $this->Form->control('nota', ['label' => 'Nota', 'required' => false]);
                        echo $this->Form->control('ch', ['label' => 'Carga horária', 'required' => false]);
                    }
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
