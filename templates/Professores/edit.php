<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor $professor
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<?php
// May be this is a temporary solution. Put into de Configuracoes table in json data format is a better solution
$departamentos = [
    'Fundamentos' => 'Fundamentos',
    'Métodos e técnicas' => 'Metodologia',
    'Política social' => 'Politicas',
]
?>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {
        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 0000.0000');
        var mask = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000.0000' : '(00) 0000.0000';
        },
        opcoes = {
            onKeyPress: function (val, e, field, options) {
                field.mask(mask.apply({}, arguments), options);
            }
        };
        $('#celular').mask(mask, opcoes);
    });
</script>

<div>
    <div class="column-responsive column-80">
        <div class="professores form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Professores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $professor->id],
                            ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome), 'class' => 'button'],
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($professor) ?>
            <fieldset>
                <h3><?= __('Editando Professor(a)') ?></h3>
                <?php
                if ($user_data['administrador_id']) :
                    echo $this->Form->control('user_id', ['type' => 'number', 'label' => false, 'hidden' => true]);
                endif;
                    echo $this->Form->control('nome', ['label' => 'Nome completo']);
                    echo $this->Form->control('cpf', ['label' => 'CPF']);
                    echo $this->Form->control('cress', ['label' => 'CRESS']);
                    echo $this->Form->control('regiao', ['label' => 'Região']);
                if ($professor->siape) {
                    echo $this->Form->control('siape', ['value' => $professor->siape, 'required' => true, 'readonly' => true]);
                } else {
                    echo $this->Form->control('siape', ['required' => true]);
                }
                if ($professor->email) {
                    echo $this->Form->control('email', ['type' => 'email', 'value' => $professor->email, 'required' => true, 'readonly' => true]);
                } else {
                    echo $this->Form->control('email', ['type' => 'email', 'required' => true]);
                }
                    echo $this->Form->control('datanascimento', ['empty' => true, 'label' => 'Data de nascimento']);
                    echo $this->Form->control('localnascimento', ['label' => 'Local de nascimento']);
                    echo $this->Form->control('ddd_telefone', ['label' => 'DDD telefone']);
                    echo $this->Form->control('telefone', ['label' => 'Telefone']);
                    echo $this->Form->control('ddd_celular', ['label' => 'DDD celular']);
                    echo $this->Form->control('celular', ['label' => 'Celular']);
                    echo $this->Form->control('homepage', ['label' => 'Homepage']);
                    echo $this->Form->control('redesocial', ['label' => 'Rede social']);
                    echo $this->Form->control('curriculolattes', ['label' => 'Curriculo Lattes']);
                    echo $this->Form->control('atualizacaolattes', ['empty' => true, 'label' => 'Atualizacao Lattes']);
                    echo $this->Form->control('curriculosigma', ['label' => 'Curriculo Sigma']);
                    echo $this->Form->control('pesquisadordgp', ['label' => 'Pesquisa Dgp']);
                    echo $this->Form->control('formacaoprofissional', ['label' => 'Formacao Profissional']);
                    echo $this->Form->control('universidadedegraduacao', ['label' => 'Universidade de graduacao']);
                    echo $this->Form->control('anoformacao', ['label' => 'Ano de formacao']);
                    echo $this->Form->control('mestradoarea', ['label' => 'Area de mestrado']);
                    echo $this->Form->control('mestradouniversidade', ['label' => 'Universidade de mestrado']);
                    echo $this->Form->control('mestradoanoconclusao', ['label' => 'Ano de conclusao de mestrado']);
                    echo $this->Form->control('doutoradoarea', ['label' => 'Area de doutorado']);
                    echo $this->Form->control('doutoradouniversidade', ['label' => 'Universidade de doutorado']);
                    echo $this->Form->control('doutoradoanoconclusao', ['label' => 'Ano de conclusao de doutorado']);
                    echo $this->Form->control('dataingresso', ['empty' => true, 'label' => 'Data de ingresso']);
                    echo $this->Form->control('formaingresso', ['label' => 'Forma de ingresso']);
                    echo $this->Form->control('tipocargo', ['label' => 'Tipo de cargo']);
                    echo $this->Form->control('regimetrabalho', ['label' => 'Regime de trabalho']);
                    echo $this->Form->control('departamento', ['label' => 'Departamento']);
                    echo $this->Form->control('dataegresso', ['empty' => true, 'label' => 'Data de egresso']);
                    echo $this->Form->control('motivoegresso', ['label' => 'Motivo de egresso']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
