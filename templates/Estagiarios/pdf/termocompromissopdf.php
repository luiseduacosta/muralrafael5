<?php
/**
 * Termo de Compromisso PDF
 * 
 * @var \App\Model\Entity\Estagiario $estagiario
 */
namespace App\View\PDF; 
use Cake\I18n\I18n;
use Cake\I18n\Date;

$hoje = Date::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->layout = 'default';
$this->assign('title', 'Termo de Compromisso');

?>

<style>
    table, th, td {
        /* border: 1px solid black; */
        border-spacing: 1px;
    }
    td {
        padding: 0px 30px 0 35px;
        text-align: center;
    }
</style>

<?php
if ($estagiario->nivel === '9'):
    $nivel = ' <b>não obrigatório</b> ';
endif;

$supervisor = is_null($estagiario->supervisor) ? "____________________" : $estagiario->supervisor->nome;
$cress = is_null($estagiario->supervisor) ? "_____" : $estagiario->supervisor->cress;

?>

<p style="text-align:center">
    <span style="font-size: 150%; font-weight: bold;">Coordenação de Estágio</span>
    <br />
    <span style="font-size: 150%; font-weight: bold;">TERMO DE COMPROMISSO</span>
</p>

<p style="text-align:justify; font-size: 90%;">
    O presente TERMO DE COMPROMISSO DE ESTÁGIO que entre si assinam a Coordenação de Estágio da Escola de Serviço Social/UFRJ, o (a) Aluno <?= trim($estagiario->aluno->nome); ?>, a  instituição <?= $estagiario->instituicao->instituicao; ?> e o (a) Supervisor (a) de Campo <?= $supervisor; ?>, visa estabelecer condições gerais que regulam a realização de ESTÁGIO OBRIGATÓRIO. Ficam estabelecidas entre as partes as seguintes condições básicas para a realização do estágio:
</p>

<p style="text-align:justify; font-size: 90%;">
    Art. 01. As atividades a serem desenvolvidas pelo (a) estagiário (a), deverão ser compatíveis com o curso de Serviço Social e norteadas pelos princípios preconizados na Política de Estágio (ABEPSS), tais como: a indissociabilidade entre as dimensões teórico-metodológica, ético-política e técnico-operativa; articulação entre Formação e Exercício Profissional; indissociabilidade entre estágio e supervisão acadêmica e de campo; articulação entre Universidade e Sociedade; unidade teoria e prática e articulação entre ensino, pesquisa e extensão.<br>
    Art. 02. As atividades desenvolvidas no campo de estágio deverão ter compatibilidade com as previstas no termo de compromisso.<br>
    Art. 03. O plano de atividades do estagiário, elaborado em acordo dos alunos, a parte concedente do estágio e a instituição de ensino, será incorporado ao termo de compromisso por meio de aditivos à medida que for avaliado, progressivamente, o desenho do aluno.<br>
    Art. 04. A quebra deste contrato, deverá ser precedida de apresentação de solicitação formal à Coordenação de Estágio, com no mínimo 1 mês de antes do término do período letivo em curso. Contendo parecer do supervisor(a) de campo e do supervisor(a) acadêmico.<br>
    Art. 05. Em caso de demissão do supervisor(a), ocorrência de férias ou licença deste profissional ao longo do período letivo, outro assistente social deverá ser imediatamente indicado para supervisão técnica do estagiário.
</p>

<h2 style="font-size: 120%; font-weight: bold;">Da ESS</h2>
<p style="text-align:justify; font-size: 90%;">
    Art. 06. De acordo com a orientação geral da Universidade Federal do Rio de Janeiro, no que concerne a estágios, e o currículo da Escola de Serviço Social, implantado em 2001:</p>
<blockquote style="text-align:justify; font-size: 90%;">§1º O estágio na UFRJ, em conformidade com o artigo 3º da resolução CEG nº 12/2018, deverá ter carga horária máxima de 20(vinte) horas por semana, podendo-se estender a 24 (vinte e quatro) horas nos casos de cursos da área da saúde;</blockquote>
<blockquote style="text-align:justify; font-size: 90%;">§2º Estágios com carga horária máxima superior ao previsto no §1º deste artigo poderão ser autorizados, pelo Conselho de Ensino de Graduação, conforme previsão na Política de Estágio, dentro do limite legal de 30(trinta) horas, em caráter excepcional.</blockquote>
<p style="text-align:justify; font-size: 90%;">
    Art. 07. Será indicado pelos Departamentos da ESS, um docente para acompanhamento acadêmico e orientação do Estagiário por meio da disciplina de Orientação ao Trabalho Acadêmico.<br>
    Art. 08. A Escola de Serviço Social fornecerá à Instituição informações e declarações solicitadas, consideradas necessárias ao bom andamento do estágio curricular.
</p>

<h2 style="font-size: 120%; font-weight: bold;">DA PARTE CONCEDENTE</h2>
<p style="text-align:justify; font-size: 90%;">
    Art. 09. O estágio será realizado no âmbito da unidade concedente onde deve existir um Assistente Social responsável pelo projeto desenvolvido pelo Serviço Social. As atividades de estágio serão realizadas em horário compatível com as atividades acadêmicas do estagiário e com as normas vigentes no âmbito da unidade concedente.<br>
    Art. 10. A Coordenação de Estágio/ESS deve ser informada com prazo de 01 (um) mês de antecedência o afastamento do supervisor(a) do campo de estágio e a indicação do seu substituto.<br>
    Art. 11. Ofertar  Instalações que tenham condições de proporcionar ao estagiário atividades de aprendizagem social, profissional e cultural.
</p>

<h2 style="font-size: 120%; font-weight: bold;">DO(A) SUPERVISOR(A) DE CAMPO</h2>
<p style="text-align:justify; font-size: 90%;">
    Art. 12. É de responsabilidade do Assistente Social supervisor(a) o acompanhamento, orientação e avaliação do aluno no campo de estágio, em conformidade com o plano de estágio, elaborado em consonância com o projeto pedagógico e com programas institucionais vinculados aos campos de estágio; garantindo diálogo permanente com o (a) supervisor (a) acadêmico (a), no processo de supervisão.<br>
    Art. 13. Ao  término de cada mês, o (a) supervisor(a) atestará à unidade de ensino, em formulário próprio, a carga horária cumprida pelo estagiário.<br>
    Art. 14. No final de cada período letivo, o (a) supervisor(a) encaminhará, ao professor(a) da disciplina de Orientação e Treinamento Profissional, avaliação do processo vivenciado pelo aluno durante o período, Instrumento este utilizado pelo professor(a) na avaliação final do aluno.
</p>

<h2 style="font-size: 120%; font-weight: bold;">DO(A) ESTAGIÁRIO(A)</h2>
<p style="text-align:justify; font-size: 90%;">
    Art. 15. Cabe ao estagiário cumprir o horário acordado com a unidade para o desempenho das atividades definidas no Plano de Estágio, observando os princípios éticos que rege o Serviço Social. São considerados motivos justos ao não cumprimento da programação, as obrigações acadêmicas do estagiário que devem ser comunicadas, ao supervisor(a), em tempo hábil.<br>
    Art. 16. O(a) aluno(a) se compromete a cuidar e manter sigilo em relação à documentação, da unidade campo de estágio, mesmo após o seu desligamento.<br>
    Art. 17. O(a) aluno(a) deverá cumprir com responsabilidade e assiduidade os compromissos assumidos junto ao acampo de estágio, independente do calendário e férias acadêmicas.<br>
    Art. 18. Aplica-se ao estagiário a legislação relacionada à saúde no trabalho, sendo sua implementação de responsabilidade da parte concedente do estágio.<br>
    Art. 19. É assegurado ao estagiário (a), sempre que o estágio tenha duração igual ou superior a 1 (um) ano, período de recesso de 30 (trinta) dias, a ser gozado preferencialmente durante suas férias escolares.
</p>

<h2 style="font-size: 120%; font-weight: bold;">DAS ORIENTAÇÕES GERAIS</h2>
<p style="text-align:justify; font-size: 90%;">
    Art. 20. O presente Termo de Compromisso terá validade de <?= strftime('%d de %B de %Y', strtotime($configuracao->termo_compromisso_inicio)); ?> a <?= strftime('%d de %B de %Y', strtotime($configuracao->termo_compromisso_final)); ?>, correspondente ao nível <?= $estagiario->nivel; ?> de Estágio. Sua interrupção antes do período previsto acarretará prejuízo para o aluno na sua avaliação acadêmica.<br>
    Art. 21. Os casos omissos serão encaminhados à Coordenação de Estágio para serem dirimidos.
</p>

<br />
<br />
<br />

<p style="text-align:right; font-size: 90%;">Rio de Janeiro, <?= $dia . ' de ' . $mes . ' de ' . $ano; ?>.</p>

<br />
<br />
<br />

<div style="center">
    <table>
        <tr>
            <td><span style="font-size: 90%; text-decoration: overline;">Coordenação de Estágio</span></td>
            <td><span style="font-size: 90%; text-decoration: overline;"><?= $estagiario->aluno->nome; ?></span></td>
            <td><span style="font-size: 90%; text-decoration: overline;"><?= $supervisor; ?></span></td>
        </tr>

        <tr>
            <td>Escola de Serviço Social</td>
            <td><span style="font-size: 90%;">DRE: <?= $estagiario->registro; ?></span></td>
            <td><span style="font-size: 90%;">CRESS 7ª Região: <?= $cress; ?></span></td>
        </tr>

        <tr>
            <td>UFRJ</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
