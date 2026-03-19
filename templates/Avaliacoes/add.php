<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao $avaliacao
 * @var \Cake\Collection\CollectionInterface|array<string> $estagiarios
 */
declare(strict_types=1);

use Cake\I18n\DateTime;
use Cake\I18n\I18n;

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<?php

I18n::setLocale('pt-BR');
$hoje = DateTime::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->setLayout('default');
$this->assign('title', 'Avaliação do Estagiário');

$supervisora = isset($estagiario->supervisor->nome);
if ($supervisora) {
    $supervisora = $estagiario->supervisor->nome;
} else {
    $supervisora = '____________________';
}

$regiao = isset($estagiario->supervisor->regiao);
if ($regiao) {
    $regiao = $estagiario->supervisor->regiao;
} else {
    $regiao = '__';
}

$cress = isset($estagiario->supervisor->cress);
if ($cress) {
    $cress = $estagiario->supervisor->cress;
} else {
    $cress = '_____';
}
?>

<div class="avaliacoes form content">

    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
            <?php if ($user_data['administrador_id'] || ($user_data['supervisor_id'] && $user_data['supervisor_id'] == $estagiario->supervisor_id)) : ?>
                <?= $this->Html->link(__('Listar avaliações'), ['action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>

    <h3><?= __('Adicionando avaliação para: ') . h($estagiario->aluno->nome) ?></h3>
    
    <div>
        <?= $this->Form->create($avaliacao) ?>
        <?php
        $this->Form->setTemplates([
            'textarea' => "<textarea class='form-control' name = '{{name}}' {{attrs}}>{{value}}</textarea>",
            'nestingLabel' => '{{input}}<label class="form-check-label" {{attrs}}>{{text}}</label>',
            'radioWrapper' => '<div class="form-check form-check-inline">{{label}}</div>',
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
            'legend' => '<legend>{{text}}</legend>',
        ]);
        ?>

        <?= $this->Form->hidden('estagiario_id', ['value' => $estagiario->id]); ?>

        <fieldset>
            <legend>
                <?= __('1) ASSIDUIDADE: Desenvolveu as atividades propostas com frequência, ausentando-se apenas com conhecimento e acordado com o(a) supervisor(a) de campo e ou acadêmico(a), seja por motivo de saúde, seja por situações estabelecidas na Lei 11788/2008, entre outras:') ?>
            </legend>
            <?= $this->Form->control('avaliacao1', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= __('2) PONTUALIDADE: cumpre horário estabelecido no Plano de Estágio:') ?>
            </legend>
            <?= $this->Form->control('avaliacao2', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= __('3) COMPROMISSO: com as ações e estratégias previstas no Plano de Estágio:') ?>
            </legend>
            <?= $this->Form->control('avaliacao3', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '4) Na relação com o(a) usuário(a): compromisso ético-político no atendimento ao usuário(a):' ?>
            </legend>
            <?= $this->Form->control('avaliacao4', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '5) Na relação com outro(a)s profissionais: Integração e articulação à equipe da área de estágio, cooperação e habilidade de trabalhar em equipe multiprofissional:' ?>
            </legend>
            <?= $this->Form->control('avaliacao5', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '6) CRITICIDADE E INICATIVA: Capacidade crítica, interventiva, propositiva e investigativa no enfrentamento das diversas questões existentes no campo de estágio:' ?>
            </legend>
            <?= $this->Form->control('avaliacao6', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '7) Apreensão do referencial teórico-metodológico, ético-político e investigativo e aplicação nas atividades inerentes ao campo e previstas no Plano de Estágio:' ?>
            </legend>
            <?= $this->Form->control('avaliacao7', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '8)  Avaliação do desempenho do(a) estagiário(a) na elaboração de relatórios, pesquisas, projetos de pesquisa e intervenção, etc:' ?>
            </legend>
            <?= $this->Form->control('avaliacao8', ['type' => 'radio', 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= '9)  As atividades previstas no Plano de Estágio em articulação com o nível de formação acadêmica foram efetuadas plenamente?' ?>
            </legend>
            <?= $this->Form->control('avaliacao9', ['type' => 'radio', 'options' => [0 => 'Sim', 1 => 'Não'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>Fundamente se achar necessário:</legend>
            <?= $this->Form->input('avaliacao9-1', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 100]); ?>

            <legend>
                <?= '10) O desempenho das atividades desenvolvidas pelo(a) estagiário(a) e o processo de supervisão foram afetados pelas condições de trabalho no campo de estágio e, em particular, pelas condições estabelecidas pelo estágio remoto?' ?>
            </legend>
            <?= $this->Form->control('avaliacao10', ['type' => 'radio', 'options' => [0 => 'Sim', 1 => 'Não'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>Justifique a resposta se achar necessário:</legend>
            <?= $this->Form->input('avaliacao10-1', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>

            <legend>
                <?= '11) Quanto à integração Disciplina de OTP/Coordenação de Estágio da ESS/Campo de Estágio: houve algum tipo de interlocução entre os 3 segmentos: aluno(a), professor(a) e supervisor(a)?' ?>
            </legend>
            <?= $this->Form->control('avaliacao11', ['type' => 'radio', 'options' => [0 => 'Sim', 1 => 'Não'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>Como você avalia esta interação? (Responda se achar necessário)</legend>
            <?= $this->Form->input('avaliacao11-1', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>

            <legend>
                <?= '12) Você recebeu e acompanhou o programa da Disciplina OTP?' ?>
            </legend>
            <?= $this->Form->control('avaliacao12', ['type' => 'radio', 'options' => [0 => 'Sim', 1 => 'Não'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>Sugestões ao que foi desenvolvido?</legend>
            <?= $this->Form->input('avaliacao12-1', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>

            <legend>
                <?= '13) Há questões que você considera que devam ser mais enfatizadas na disciplina de OTP?' ?>
            </legend>
            <?= $this->Form->control('avaliacao13', ['type' => 'radio', 'options' => [0 => 'Sim', 1 => 'Não'], 'class' => 'form-check-input', 'label' => false]); ?>

            <legend>
                <?= 'Se sim, quais?' ?>
            </legend>
            <?= $this->Form->input('avaliacao13-1', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>

            <legend>
                <?= '14) Como avalia a experiência do estágio remoto neste semestre? Será possível a continuidade do estágio na modalidade remota no próximo semestre?' ?>
            </legend>
            <?= $this->Form->input('avaliacao14', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>

            <legend>
                <?= '15) Sugestões e observações:' ?>
            </legend>
            <?= $this->Form->input('observacoes', ['type' => 'textarea', 'label' => false, 'required' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?>
        </fieldset>

        <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>