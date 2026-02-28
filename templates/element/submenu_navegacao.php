<?php

declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }

// $user_data['administrador_id'] || $user_data['aluno_id'] || $user_data['professor_id'] || $user_data['supervisor_id']
    
?>
<!-- templates/element/submenu_navegacao.php -->
<script>
    addEventListener('load', () => {
        /* sub menu unselect */
        const navInputs = [...document.querySelectorAll('.toggle-input:not(#nav-toggler)')];
        const unselect = (inputBox) => { inputBox.checked = false };
        const unselectAll = (event) => { navInputs.forEach( (inputBox) => { 
            if (inputBox !== event.target) unselect(inputBox) 
        })};
        addEventListener('mouseup', unselectAll);
        addEventListener('touchend', unselectAll);
    });
</script>
    
<nav>
    <?php 
        $logo = $this->Html->image('logoess_horizontal-azul.svg', ['height' => '50', 'width' => '150', 'alt' => 'ESS']);
        echo $this->Html->link($logo, $this->getRequest()->getRequestTarget() == '/' ? "http://www.ess.ufrj.br" : '/', ['escape' => false, 'full'=>true]);
    ?>

    <label for="nav-toggler" class="responsive-toggle-label toggle-icon">☰</label>
    <input id="nav-toggler" type="checkbox" class="toggle-input" />
    
    <menu class="responsive-toggle-dropdown">

        <li><?php echo $this->Html->link("Mural", ['controller' => 'Muralestagios', 'action' => 'index']); ?></li>

        <li class="menu-declaracoes">
            <input id="menu-declaracoes-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-declaracoes-toggler" class="toggle-label">Declarações <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                <li><?php echo $this->Html->link("Termo de compromisso", "/Inscricoes/termosolicita"); ?></li>
                <li><?php echo $this->Html->link("Folha de avaliação discente", "/Alunos/avaliacaosolicita"); ?></li>
                <li><?php echo $this->Html->link("Avaliação discente on-line", "/Avaliacoes/busca_dre"); ?></li>
                <li><?php echo $this->Html->link("Folha de atividades", "/Alunos/folhasolicita"); ?></li>
                <li><?php echo $this->Html->link("Folha de atividades on-line", "/folhadeatividades/busca_dre"); ?></li>

                <?php if ($user_data['administrador_id'] || $user_data['professor_id'] || $user_data['supervisor_id']): ?>
                    <li><?php echo $this->Html->link("Declaração de estágio", "/Alunos/busca_dre"); ?></li>
                <?php else: ?>
                    <li><?php echo $this->Html->link("Declaração de estágio", "/Estagiarios/view?registro="); ?></li>
                <?php endif; ?>
                
            </menu>
        </li>

        <li class="menu-consulta">
            <input id="menu-consulta-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-consulta-toggler" class="toggle-label">Consulta <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                
                <li><?php echo $this->Html->link("Inscrições", ['controller' => 'Inscricoes', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link("Instituições", ['controller' => 'Instituicoes', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link("Estagiários", ['controller' => 'Estagiarios', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link("Alunos", ['controller' => 'Alunos', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link("Professores", ['controller' => 'Professores', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link("Supervisores", ['controller' => 'Supervisores', 'action' => 'index']); ?></li>
        
            </menu>
        </li>
        
        <li><?php echo $this->Html->link('Grupo Google', 'https://groups.google.com/forum/#!forum/estagio_ess'); ?></li>
        
        <?php if ($user_data['administrador_id']): ?>
            <li class="menu-admin">
                <input id="menu-admin-toggler" type="checkbox" class="toggle-input" />
                <label for="menu-admin-toggler" class="toggle-label">Administração <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
                
                <menu class="toggle-dropdown">
                    <li><?php echo $this->Html->link('Configurações', '/Configuracoes'); ?></li>
                    <li><?php echo $this->Html->link('Usuários', '/Users'); ?></li>
                    <li><?php echo $this->Html->link('Planilha seguro', '/Alunos/planilhaseguro/'); ?></li>
                    <li><?php echo $this->Html->link('Planilha CRESS', '/Alunos/planilhacress/'); ?></li>
                    <li><?php echo $this->Html->link('Carga horária', '/Alunos/cargahoraria/'); ?></li>
                    <li><?php echo $this->Html->link('Complemento período', '/Complementos'); ?></li>
                </menu>
            </li>
        <?php else: ?>
            <li><?php echo $this->Html->link('Fale conosco', 'mailto:estagio@ess.ufrj.br'); ?></li>
        <?php endif; ?>

        <li class="menu-user">
            
            <input id="menu-user-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-user-toggler" class="toggle-label">Usuário <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                
                <?php if ($user_session): ?>
                    <li><?php echo $this->Html->link("Minha conta", ['controller' => 'Users', 'action' => 'view', $user_session->id]); ?></li>
                    <li><?php echo $this->Html->link('Sair (' . $user_session->get('email') . ')', ['controller' => 'Users', 'action' => 'logout']); ?></li>
                <?php else: ?>
                    <li><?php echo $this->Html->link("Login", ['controller' => 'Users', 'action' => 'login']); ?></li>
                <?php endif; ?>
            </menu>  
        </li>    
    </menu>
</nav>
