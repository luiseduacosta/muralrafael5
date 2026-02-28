<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao $avaliacao
 * @var \Cake\Collection\CollectionInterface|string[] $estagiarios
 */
// pr($estagiario);
// die();
?>
<?php
$dia = strftime('%e', time());
$mes = strftime('%B', time());
$ano = strftime('%Y', time());

$supervisora = isset($estagiario->supervisor->nome);
if ($supervisora) {
    $supervisora = $estagiario->supervisor->nome;
} else {
    $supervisora = "____________________";
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
            <?= $this->Html->link(__('Listar avaliações'), ['action' => 'index/' . $estagiario->id . '/' . $estagiario->registro], ['class' => 'button']) ?>
        </div>
    </aside>

    
    <h3><?= __('Adicionando avaliação') ?></h3>
    
    <div>
        <?= $this->Form->create($avaliacao) ?>
        <?php
        $this->Form->setTemplates([
            "textarea" => "<textarea class='form-control' name = '{{name}}' {{attrs}}>{{value}}</textarea>",
            'nestingLabel' => '{{hidden}}<label class="form-check-label" {{attrs}}>{{text}}</label>',
            'radioWrapper' => '{{label}}{{input}}',
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
            'legend' => '<legend>{{text}}</legend>',
        ]);
        ?>

        <?= $this->Form->control('estagiario_id', ['options' => [$estagiario->id => $estagiario->aluno->nome]]); ?>

        <fieldset>
            <legend><?= __('1) ASSIDUIDADE: Desenvolveu as atividades propostas com frequência, ausentando-se apenas com conhecimento e acordado com o(a) supervisor(a) de campo e ou acadêmico(a), seja por motivo de saúde, seja por situações estabelecidas na Lei 11788/2008, entre outras:') ?></legend>
            <p><?= $this->Form->input('avaliacao1', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= __('2) PONTUALIDADE: cumpre horário estabelecido no Plano de Estágio:') ?></legend>
            <p><?= $this->Form->input('avaliacao2', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= __('3) COMPROMISSO: com as ações e estratégias previstas no Plano de Estágio:') ?></legend>
            <p><?= $this->Form->input('avaliacao3', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('4) Na relação com o(a) usuário(a): compromisso ético-político no atendimento ao usuário(a):') ?></legend>
            <p><?= $this->Form->input('avaliacao4', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('5) Na relação com outro(a)s profissionais: Integração e articulação à equipe da área de estágio, cooperação e habilidade de trabalhar em equipe multiprofissional:') ?></legend>
            <p><?= $this->Form->input('avaliacao5', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('6) CRITICIDADE E INICATIVA: Capacidade crítica, interventiva, propositiva e investigativa no enfrentamento das diversas questões existentes no campo de estágio:') ?></legend>
            <p><?= $this->Form->input('avaliacao6', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('7) Apreensão do referencial teórico-metodológico, ético-político e investigativo e aplicação nas atividades inerentes ao campo e previstas no Plano de Estágio:') ?></legend>
            <p><?= $this->Form->input('avaliacao7', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('8)  Avaliação do desempenho do(a) estagiário(a) na elaboração de relatórios, pesquisas, projetos de pesquisa e intervenção, etc:') ?></legend>
            <p><?= $this->Form->input('avaliacao8', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Ruim', 1 => 'Regular', 2 => 'Bom', 3 => 'Excelente']]); ?></p>
            
            <legend><?= ('9)  As atividades previstas no Plano de Estágio em articulação com o nível de formação acadêmica foram efetuadas plenamente?') ?></legend>
            <p><?= $this->Form->input('avaliacao9', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Sim', 1 => 'Não']]); ?></p>
            <p>Fundamente se achar necessário: </p>
            <p><?= $this->Form->input('avaliacao9-1', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 100]); ?></p>
            
            <legend><?= ('10) O desempenho das atividades desenvolvidas pelo(a) estagiário(a) e o processo de supervisão foram afetados pelas condições de trabalho no campo de estágio e, em particular, pelas condições estabelecidas pelo estágio remoto?') ?></legend>
            <p><?= $this->Form->input('avaliacao10', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Sim', 1 => 'Não']]); ?></p>
            <p>Justifique a resposta se achar necessário:</p>
            <p><?= $this->Form->input('avaliacao10-1', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
            
            <legend><?= ('11) Quanto à integração Disciplina de OTP/Coordenação de Estágio da ESS/Campo de Estágio: houve algum tipo de interlocução entre os 3 segmentos: aluno(a), professor(a) e supervisor(a)?') ?></legend>
            <p><?= $this->Form->input('avaliacao11', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Sim', 1 => 'Não']]); ?></p>
            <p>Como você avalia esta interação? (Responda se achar necessário)</p>
            <p><?= $this->Form->input('avaliacao11-1', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
            
            <legend><?= ('12) Você recebeu e acompanhou o programa da Disciplina OTP?') ?></legend>
            <p><?= $this->Form->input('avaliacao12', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Sim', 1 => 'Não']]); ?></p>
            <p>Sugestões ao que foi desenvolvido?</p>
            <p><?= $this->Form->input('avaliacao12-1', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
            
            <legend><?= ('13) Há questões que você considera que devam ser mais enfatizadas na disciplina de OTP?') ?></legend>
            <p><?= $this->Form->input('avaliacao13', ['type' => 'radio', 'legend' => false, 'options' => [0 => 'Sim', 1 => 'Não']]); ?></p>
            
            <legend>Se sim, quais?</legend>
            <p><?= $this->Form->input('avaliacao13-1', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
            
            <legend>14) Como avalia a experiência do estágio remoto neste semestre? Será possível a continuidade do estágio na modalidade remota no próximo semestre?') </legend>
            <p><?= $this->Form->input('avaliacao14', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
            
            <legend>15) Sugestões e observações:</legend>
            <p><?= $this->Form->input('observacoes', ['type' => 'textarea', 'label' => false, 'class' => 'form-control', 'rows' => 5, 'cols' => 60]); ?></p>
        </fieldset>
        
        <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>