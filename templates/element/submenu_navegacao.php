<?php

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => 0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
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
        echo $this->Html->link($logo, $this->getRequest()->getRequestTarget() == '/' ? 'http://www.ess.ufrj.br' : '/', ['escape' => false, 'full' => true]);
    ?>

    <label for="nav-toggler" class="responsive-toggle-label toggle-icon">☰</label>
    <input id="nav-toggler" type="checkbox" class="toggle-input" />
    
    <menu class="responsive-toggle-dropdown">

        <li><?php echo $this->Html->link('Mural', ['controller' => 'Muralestagios', 'action' => 'index']); ?></li>

        <?php if ($user_data['categoria'] == 1 || $user_data['categoria'] == 2) : ?>
        <li class="menu-declaracoes">
            <input id="menu-declaracoes-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-declaracoes-toggler" class="toggle-label">Declarações <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                
                    <li><?php echo $this->Html->link('Declaração de periódo', ['controller' => 'Alunos', 'action' => 'declaracaoperiodo']); ?></li>
                    <li><?php echo $this->Html->link('Termo de compromisso', ['controller' => 'Estagiarios', 'action' => 'termocompromisso']); ?></li>
                    <li><?php echo $this->Html->link('Declaração de estágio', ['controller' => 'Estagiarios', 'action' => 'declaracaodeestagiopdf']); ?></li>
                    <li><?php echo $this->Html->link('Folha de atividades', ['controller' => 'Folhadeatividades', 'action' => 'index']); ?></li>
                    <li><?php echo $this->Html->link('Avaliação discente', ['controller' => 'Avaliacoes', 'action' => 'index']); ?></li>

            </menu>
        </li>
        <?php endif; ?>

        <?php if ($user_data['categoria'] == 1) : ?>
        <li class="menu-consulta">
            <input id="menu-consulta-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-consulta-toggler" class="toggle-label">Consulta <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                
                <li><?php echo $this->Html->link('Alunos', ['controller' => 'Alunos', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link('Supervisores', ['controller' => 'Supervisores', 'action' => 'index']); ?></li>                
                <li><?php echo $this->Html->link('Instituições', ['controller' => 'Instituicoes', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link('Inscrições', ['controller' => 'Inscricoes', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link('Estagiários', ['controller' => 'Estagiarios', 'action' => 'index']); ?></li>
                <li><?php echo $this->Html->link('Professores', ['controller' => 'Professores', 'action' => 'index']); ?></li>
        
            </menu>
        </li>
        <?php endif; ?>

        <?php if ($user_data['categoria'] == 2) { ?>
                <li><?php echo $this->Html->link('Meus dados', ['controller' => 'Alunos', 'action' => 'view', $user_data['aluno_id']]); ?></li>
        <?php } ?>

        <?php if ($user_data['categoria'] == 3) { ?>
                <li><?php echo $this->Html->link('Meus dados', ['controller' => 'Professores', 'action' => 'view', $user_data['professor_id']]); ?></li>
        <?php } ?>

        <?php if ($user_data['categoria'] == 4) { ?>
                <li><?php echo $this->Html->link('Meus dados', ['controller' => 'Supervisores', 'action' => 'view', $user_data['supervisor_id']]); ?></li>
        <?php } ?>
        
        <li><?php echo $this->Html->link('Grupo Google', 'https://groups.google.com/forum/#!forum/estagio_ess'); ?></li>
        
        <?php if ($user_data['categoria'] == 1) : ?>
            <li class="menu-admin">
                <input id="menu-admin-toggler" type="checkbox" class="toggle-input" />
                <label for="menu-admin-toggler" class="toggle-label">Administração <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
                
                <menu class="toggle-dropdown">
                    <li><?php echo $this->Html->link('Configurações', ['controller' => 'Configuracoes', 'action' => 'view', 1]); ?></li>
                    <li><?php echo $this->Html->link('Usuários', ['controller' => 'Users', 'action' => 'index']); ?></li>
                    <li><?php echo $this->Html->link('Planilha seguro', ['controller' => 'Alunos', 'action' => 'planilhaseguro']); ?></li>
                    <li><?php echo $this->Html->link('Planilha CRESS', ['controller' => 'Alunos', 'action' => 'planilhacress']); ?></li>
                    <li><?php echo $this->Html->link('Carga horária', ['controller' => 'Alunos', 'action' => 'cargahoraria']); ?></li>
                    <li><?php echo $this->Html->link('Complemento período', ['controller' => 'Complementos', 'action' => 'index']); ?></li>
                </menu>
            </li>
        <?php else : ?>
            <li><?php echo $this->Html->link('Fale conosco', 'mailto:[EMAIL_ADDRESS]'); ?></li>
        <?php endif; ?>

        <li class="menu-user">
            
            <input id="menu-user-toggler" type="checkbox" class="toggle-input" />
            <label for="menu-user-toggler" class="toggle-label">Usuário <span class="toggle-more">▾</span><span class="toggle-less">◂</span></label>
            
            <menu class="toggle-dropdown">
                
                <?php if ($user_session) : ?>
                    <?php if ($this->request->getSession()->check('Auth.impersonating')) : ?>
                        <li><?php echo $this->Html->link('Retornar ao Administrador', ['controller' => 'Users', 'action' => 'alternarusuario'], ['style' => 'color: #ffc107; font-weight: bold;']); ?></li>
                    <?php endif; ?>
                    <li><?php echo $this->Html->link('Minha conta', ['controller' => 'Users', 'action' => 'view', $user_session->id]); ?></li>
                    <li><?php echo $this->Html->link('Sair (' . $user_session->get('email') . ')', ['controller' => 'Users', 'action' => 'logout']); ?></li>
                <?php else : ?>
                    <li><?php echo $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login']); ?></li>
                <?php endif; ?>
            </menu>  
        </li>    
    </menu>
</nav>
