<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao $avaliacao
 */
namespace App\View\PDF; 
use Cake\I18n\I18n;
use Cake\I18n\Date;

I18n::setLocale('pt-BR');
$hoje = Date::now('America/Sao_Paulo');

$dia = $hoje->i18nFormat('d');
$mes = $hoje->i18nFormat('MMMM');
$ano = $hoje->i18nFormat('Y');

$this->layout = 'default';
$this->assign('title', 'Avaliação do(a) Aluno(a)');

?>

<h2 style="text-align:center;">
<br />
<span style="font-size: 110%">Coordenação de Estágio</span><br />
<span style="font-size: 90%">Avaliação final do(a) supervisor(a) de campo do desempenho discente</span>
</h2>
    
<p style="line-height:90%; font-size: 90%; text-align:justify">
Nome do(a) Estudante: <?= $estagiario->aluno->nome; ?><br>
Supervisor(a) de Campo: <?= $estagiario->supervisor->nome ?? '____________________'; ?> CRESS: <?= $estagiario->supervisor->cress ?? '_____'; ?><br />
E-mail: <?= $estagiario->supervisor->email ?? '____________________'; ?> Telefone: <?= $estagiario->supervisor->telefone ?? ''; ?> Celular: <?= $estagiario->supervisor->celular ?? ''; ?><br />
Campo de Estágio: <?= $estagiario->instituicao->instituicao ?? '____________________'; ?><br />
Endereço Institucional: <?= $estagiario->instituicao->endereco ?? ''; ?><br />
Período de realização do estágio: <?= $estagiario->periodo; ?><br />
Nível de Estágio: <?= $estagiario->nivel; ?><br />
Supervisor(a) Acadêmico(a): <?= $estagiario->professor->nome ?? '____________________'; ?>
</p>

<p style="line-height:100%; font-size: 90%;">
<b>Leia atentamente cada item e marque um X na posição que melhor descreva os resultados alcançados com a inserção do(a) aluno(a) no campo de estágio.</b>
</p>

<div style='text-align: center; background-color: 009900;' >
<p style="line-height: 100%; font-size:100%; font-">Desempenho discente no espaço ocupacional</p>
</div>

<p style="line-height: 1; text-align:justify; font-size:80%">
1) Sobre assiduidade: manteve a frequência, ausentando-se apenas com conhecimento da supervisão de campo e acadêmica, seja por motivo de saúde ou por situações estabelecidas na Lei 11788/2008, entre outras:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
2) Sobre pontualidade: cumpre o horário estabelecido no Plano de Estágio:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
3)  Sobre compromisso: possui compromisso com as ações e estratégias previstas no Plano de Estágio:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
4 ) Na relação com usuários(as): compromisso ético-político no atendimento:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
5) Na relação com profissionais: integração e articulação à equipe de estágio, cooperação e habilidade para trabalhar em equipe multiprofissional:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
6) Sobre criticidade e iniciativa: possui capacidade crítica, interventiva, propositiva e investigativa no enfrentamento das diversas questões existentes no campo de estágio:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
7) Apreensão do referencial teórico-metodológico, ético-político e investigativo, e aplicação nas atividades inerentes ao campo e previstas no Plano de Estágio:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
8)  Avaliação do desempenho na elaboração de relatórios, pesquisas, projetos de pesquisa e intervenção, etc:
<br style="line-height: 100%;">
(  ) Ruim   (   ) Regular  (  ) Bom (  ) Excelente
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
9) O plano de estágio foi elaborado pela supervisão de campo, estudante e com apoio da supervisão acadêmica no início do semestre?
<br style="line-height: 100%;">
(   ) sim   (   ) não. 
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
10) As atividades previstas no Plano de Estágio em articulação com o nível de formação acadêmica foram efetuadas plenamente?
<br style="line-height: 100%;">
(   ) sim   (   ) não. 
</p>
<hr>

<p style="line-height: 1; text-align:justify; font-size:80%">
11) O desempenho das atividades desenvolvidas pelo/a discente e o processo de supervisão foram afetados pelas condições de trabalho?
<br style="line-height: 100%;">
(   ) sim   (   ) não. 
</p>

<br />
<br />

<div style='text-align: center; background-color: 009900'>
<p style="line-height: 100%; font-size:100%">Relação interinstitucional</p>
</div>

<p style="line-height: 1; text-align:justify; font-size:80%">
1) Quanto à integração sala de aula/campo de estágio, houve alguma interlocução entre discente, docente e supervisão de campo?
<br style="line-height: 100%;">
(   ) sim   (   ) não.
</p>
<hr>
<p style="line-height: 1; text-align:justify; font-size:80%;">
2) Quanto à integração Coordenação de estágio/campo de estágio: houve algum tipo de interlocução?
<br style="line-height: 100%;">
(   ) sim   (   ) não. 
</p>
<hr>
<p style="line-height: 1; text-align:justify; font-size:80%;">
3) Você tomou conhecimento do conteúdo da Disciplina de OTP?
<br style="line-height: 100%;">
(  ) Sim     (   ) Não
</p>
<hr>
<p style="line-height: 1; text-align:justify; font-size:80%;">
4) Você participou de alguma atividade promovida e/ou convocada por docente ou Coordenação de Estágio (reuniões, Fórum Local de Estágio, cursos, eventos, entre outros)?
(  ) Sim     (   ) Não
<br style="line-height: 100%;">
</p>
<p style="line-height: 1; text-align:justify; font-size:80%;">
Caso positivo, por favor, informe qual:
</p>
<br />
<hr>
<br />
<hr>
<br />
<hr>
<br />
<p style="line-height: 1; text-align:justify; font-size:80%;">
5) Há questões que você considera que devam ser mais enfatizadas na disciplina de OTP?
<br style="line-height: 100%;">
(  ) Sim     (   ) Não
</p>
<p style="line-height: 1; text-align:justify; font-size:80%;">
Caso positivo, por favor, informe quais:
</p>
<br />
<hr>
<br />
<hr>
<br />
<hr>
<br />
<p style="line-height: 1; text-align:justify; font-size:80%;">
6) De modo geral, como avalia a experiência do estágio neste semestre? Será possível a continuidade no próximo? Aproveite este espaço para deixar suas críticas, sugestões e/ou observações:
</p>    
<br />
<hr>
<br />
<hr>
<br />
<hr>
<br />

<br />
<br />
<p style="text-align:right; line-height:100%; font-size: 90%;">Rio de Janeiro, <?= $dia; ?> de <?= $mes; ?> de <?= $ano; ?></p>

<br />
<br />
<br />
<br />

<table style="width:100%">
<tr>
<td class='text-center'><span style="font-size: 90%; text-decoration: overline;"><?= $estagiario->aluno->nome; ?></span></td>
<td class='text-center'><span style="font-size: 90%; text-decoration: overline;"><?= $estagiario->supervisor->nome; ?></span></td>
</tr>

<tr>
<td class='text-center'><span style="font-size: 90%;">DRE: <?= $estagiario->aluno->registro; ?></span></td>
<td class='text-center'><span style="font-size: 90%;">Supervisão de campo<br> CRESS: <?= $estagiario->supervisor->cress; ?></span></td>
</tr>

</table>
