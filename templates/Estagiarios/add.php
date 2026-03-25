<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */
declare(strict_types=1);

use Cake\I18n\DateTime;

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
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
                    <?= $this->Html->link(__('Listar Estagiarios'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>

            <?= $this->Form->create($estagiario) ?>
            <fieldset>
                <h3><?= __('Adicionando Estagiario') ?></h3>
                <?php
                    echo $this->Form->hidden('aluno_id', ['value' => $aluno->id]);
                    echo $this->Form->control('aluno_nome', ['label' => 'Aluno', 'value' => $aluno->nome, 'readonly' => true]);
                    echo $this->Form->control('registro', ['value' => $aluno->registro, 'readonly']);
                    echo $this->Form->control('nivel', ['value' => $estagiario->nivel, 'readonly' => true, 'required' => true]);
                    echo $this->Form->control('ajuste2020', ['options' => ['1' => 'Sim (3 semestres)', '0' => 'Não (4 semestres)'], 'value' => $aluno->ajuste2020, 'readonly']);
                    echo $this->Form->control('tc', ['label' => 'Termo de compromisso assinado S/N', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'default' => '0','required' => false]);
                    echo $this->Form->control('tc_solicitacao', ['label' => 'Data de Solicitação', 'value' => DateTime::now()->format('Y-m-d'), 'readonly' => true]);
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes, 'class' => 'form-control']);
                    echo $this->Form->control('supervisor_id', ['options' => $supervisores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('professor_id', ['options' => $professores, 'empty' => true, 'class' => 'form-control']);
                    echo $this->Form->control('periodo', ['value' => $periodo, 'readonly' => true]);
                if ($user_data['categoria'] == '1') {
                    echo $this->Form->control('nota', ['required' => false, 'readonly' => true, 'label' => 'Nota']);
                    echo $this->Form->control('ch', ['required' => false, 'readonly' => true, 'label' => 'CH']);
                }
                    echo $this->Form->control('benetransporte', ['label' => 'Transporte', 'required' => false, 'empty' => true, 'default' => null]);
                    echo $this->Form->control('benealimentacao', ['label' => 'Alimentação', 'required' => false, 'empty' => true, 'default' => null]);
                    echo $this->Form->control('benebolsa', ['label' => 'Valor do benefício de Bolsa']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
