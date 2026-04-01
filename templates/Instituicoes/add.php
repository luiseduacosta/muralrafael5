<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao $instituicao
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<!-- Needs this style to show the icons -->
<style>
    .editor-toolbar button {
        color: #333 !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {
        $('#cnpj').mask('00.000.000/0000-00', {placeholder: '00.000.000/0000-00'});
        $('#cep').mask('00000-000', {placeholder: '00000-000'});
        const easyMDE = new EasyMDE({element: document.getElementById('observacoes')});
    });
</script>

<div>
    <div class="column-responsive column-80">
        <div class="instituicoes form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Instituicoes'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($instituicao) ?>
            <fieldset>
                <h3><?= __('Adicionar Instituição') ?></h3>
                <?php
                    echo $this->Form->control('instituicao', ['label' => 'SIGLA - Instituição']);
                    echo $this->Form->control('area_id', ['options' => $areas, 'empty' => true, 'label' => 'Área']);
                    echo $this->Form->control('natureza', ['label' => 'Natureza: pública, privada, militar, municipal']);
                    echo $this->Form->control('cnpj', ['label' => 'CNPJ', 'pattern' => '[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}', 'placeholder' => '00.000.000/0000-00', 'label' => 'CNPJ']);
                    echo $this->Form->control('telefone', ['label' => 'Telefone']);
                    echo $this->Form->control('email', ['label' => 'Email']);
                    echo $this->Form->control('url', ['label' => 'Site da instituição']);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'pattern' => '[0-9]{5}\-[0-9]{3}', 'placeholder' => '00000-000']);
                    echo $this->Form->control('endereco', ['label' => 'Endereço']);
                    echo $this->Form->control('bairro', ['label' => 'Bairro']);
                    echo $this->Form->control('municipio', ['label' => 'Município']);
                    echo $this->Form->control('beneficios', ['label' => 'Benefícios oferecido pela instituição', 'required' => false]);
                    echo $this->Form->control('fim_de_semana', ['label' => 'Estágio no fim de semana', 'options' => ['1' => 'Sim', '0' => 'Nao', '2' => 'Parcial']]);
                    echo $this->Form->control('local_inscricao', ['label' => 'Local de inscrição', 'options' => ['1' => 'Coordenação de Estágio/ESS/UFRJ', '0' => 'Instituição']]);
                    echo $this->Form->control('convenio', ['label' => 'Nº do convênio na UFRJ', 'required' => false]);
                    echo $this->Form->control('expira', ['label' => 'Data de expiração do convênio', 'empty' => true]);
                    echo $this->Form->control('seguro', ['label' => 'Seguro (S/N)', 'options' => ['1' => 'Sim', '0' => 'Nao'], 'default' => '0']);
                    echo $this->Form->control('avaliacao', ['label' => 'Avaliação', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], 'default' => '3']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações']);
                    echo $this->Form->control('supervisores._ids', ['options' => $supervisores]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
