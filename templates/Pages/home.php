<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.10.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->disableAutoLayout();

$checkConnection = function (string $name) {
    $error = null;
    $connected = false;
    try {
        ConnectionManager::get($name)->getDriver()->connect();
        // No exception means success
        $connected = true;
    } catch (Exception $connectionError) {
        $error = $connectionError->getMessage();
        if (method_exists($connectionError, 'getAttributes')) {
            $attributes = $connectionError->getAttributes();
            if (isset($attributes['message'])) {
                $error .= '<br />' . $attributes['message'];
            }
        }
        if ($name === 'debug_kit') {
            $error = 'Try adding your current <b>top level domain</b> to the
                <a href="https://book.cakephp.org/debugkit/5/en/index.html#configuration" target="_blank">DebugKit.safeTld</a>
            config and reload.';
            if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
                $error .= '<br />You need to install the PHP extension <code>pdo_sqlite</code> so DebugKit can work properly.';
            }
        }
    }

    return compact('connected', 'error');
};

$cakeDescription = $configuracao['descricao'] . ' - ' . $configuracao['instituicao'];

$user_session = $this->request->getAttribute('identity');
    
?>

<!DOCTYPE html>
<!-- templates/pages/home -->
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'fonts', 'milligram.min', 'cake', 'bootstrap', 'mural', 'nav']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    
    <?= $this->element('submenu_navegacao'); ?>
    
    <div id="content">
        <?= $this->Flash->render() ?>
        <header>
            <div>
                <div class="row">
                    <div class="col">
                        <h1 class="text-center">Boas vindas ao <a href="muralestagios">Mural de Estágios</a></h1>
                        <h2 class="text-center">
                                
                            <?php if (!$user_session) : ?>
                                <?= $this->Html->link(__('Login'), ['controller' => 'users', 'action' => 'login'], ['class' => 'button btn-info']) ?>
                                <?= $this->Html->link(__('Novo usuário'), ['controller' => 'users', 'action' => 'add'], ['class' => 'button']) ?>
                            <?php else : ?>
                                <?= $this->Html->link(__('Minha Conta'), ['controller' => 'users', 'action' => 'view', $user_session->id], ['class' => 'button']) ?>
                                <?= $this->Html->link(__('Logout'), ['controller' => 'users', 'action' => 'logout'], ['class' => 'button btn-info']) ?>
                            <?php endif; ?> 
                        </h2>
                    </div>   
                    <div class="col">
                        <p>Prezadas(os) usuárias(os),</p>
                        <p>O Mural de Estágio tem a função de: <br />permitir a consulta e inscrição em vagas de estágio; <br />retirar o Termo de Compromisso, folha de atividades, avaliação do/a supervisor/a, declaração de estágio, dentre outros.</p>
                        <p>É a sua primeira vez por aqui? <br />Faça o cadastro com dados completos, não abrevie seu nome.</p>
                        <p>Vai retirar o Termo de Compromisso? <br />Preencha os dados da supervisão de campo e do/a docente de OTP.</p>
                        <p>Supervisores e docentes também podem fazer o cadastro e contribuir para mantermos atualizados os dados das instituições, assim como seus dados profissionais, incluindo e-mail e telefone.</p>
                        <p>Ficou alguma dúvida? <br />Escreva um e-mail detalhado para: <?= $this->Text->autoLinkEmails('estagio@ess.ufrj.br') ?>.</p>
                        <p>Estamos à disposição.</p>
                    </div>
                </div>
            </div>
        </header>
    
        <?php if (Configure::read('debug')) : ?>
        <main class="main">
            
            <div class="message default-cursor default text-center" >
                <p>Atenção: a seção abaixo não irá aparecer se o modo debug estiver desligado.</p>
            </div>
            <div>
                <div class="content">
                    <div class="row">
                        <div class="column">
                            <div id="url-rewriting-warning">
                                <ul>
                                    <li class="bullet problem">
                                        URL rewriting is not properly configured on your server.<br />
                                        1) <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/installation.html#url-rewriting">Help me configure it</a><br />
                                        2) <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/development/configuration.html#general-configuration">I don't / can't use URL rewriting</a>
                                    </li>
                                </ul>
                            </div>
                            <?php Debugger::checkSecurityKeys(); ?>
                        </div>
                    </div>

                    <h3>Mapa do site</h3>
                    <p>
                        <a href="./administradores">administradores</a> | <a href="./administradores/view/1">view</a> | <a href="./administradores/edit/1">edit</a><br />
                        <a href="./alunos">alunos</a> | <a href="./alunos/view/1">view</a> | <a href="./alunos/edit/1">edit</a> | <a href="./alunos/add">add</a> | <a href="./alunos/busca">busca</a> | <a href="./alunos/planilhacress">planilhacress</a> | <a href="./alunos/planilhaseguro">planilhaseguro</a> | <a href="./alunos/cargahoraria">cargahoraria</a> | <a href="./alunos/declaracaoperiodo/1">declaracaoperiodo</a> | <a href="./alunos/folhasolicita">folhasolicita</a> | <a href="./alunos/avaliacaosolicita">avaliacaosolicita</a><br />
                        <a href="./areas">areas</a> | <a href="./areas/view/1">view</a> | <a href="./areas/edit/1">edit</a> | <a href="./areas/add">add</a><br />
                        <a href="./avaliacoes">avaliacoes</a> | <a href="./avaliacoes/view/1">view</a> | <a href="./avaliacoes/edit/1">edit</a> | <a href="./avaliacoes/add">add</a><br />
                        <a href="./complementos">complementos</a> | <a href="./complementos/view/1">view</a> | <a href="./complementos/edit/1">edit</a> | <a href="./complementos/add">add</a><br />
                        <a href="./configuracoes">configuracoes</a> | <a href="./configuracoes/edit/1">edit</a><br />
                        <a href="./estagiarios">estagiarios</a> | <a href="./estagiarios/view/1">view</a> | <a href="./estagiarios/edit/1">edit</a> | <a href="./estagiarios/add">add</a> | <a href="./estagiarios/termodecompromisso/1">termodecompromisso</a> | <a href="./estagiarios/selecionadeclaracaodeestagio/1">selecionadeclaracaodeestagio</a><br />
                        <a href="./folhadeatividades">folhadeatividades</a> | <a href="./folhadeatividades/view/1">view</a> | <a href="./folhadeatividades/edit/1">edit</a> | <a href="./folhadeatividades/add">add</a> | <a href="./folhadeatividades/selecionafolhadeatividades">selecionafolhadeatividades</a><br />
                        <a href="./inscricoes">inscricoes</a> | <a href="./inscricoes/view/1">view</a> | <a href="./inscricoes/edit/1">edit</a> | <a href="./inscricoes/add">add</a> | <a href="./inscricoes/termocompromisso/1">termocompromisso</a><br />
                        <a href="./instituicoes">instituicoes</a> | <a href="./instituicoes/view/1">view</a> | <a href="./instituicoes/edit/1">edit</a> | <a href="./instituicoes/add">add</a><br />
                        <a href="./muralestagios">muralestagios</a> | <a href="./muralestagios/view/1">view</a> | <a href="./muralestagios/edit/1">edit</a> | <a href="./muralestagios/add">add</a><br />
                        <a href="./professores">professores</a> | <a href="./professores/view/1">view</a> | <a href="./professores/edit/1">edit</a> | <a href="./professores/add">add</a><br />
                        <a href="./supervisores">supervisores</a> | <a href="./supervisores/view/1">view</a> | <a href="./supervisores/edit/1">edit</a> | <a href="./supervisores/add">add</a><br />
                        <a href="./turmas">turmas</a> | <a href="./turmas/view/1">view</a> | <a href="./turmas/edit/1">edit</a> | <a href="./turmas/add">add</a><br />
                        <a href="./turnos">turnos</a> | <a href="./turnos/view/1">view</a> | <a href="./turnos/edit/1">edit</a> | <a href="./turnos/add">add</a><br />
                        <a href="./users">users</a> | <a href="./users/view/1">view</a> | <a href="./users/edit/1">edit</a> | <a href="./users/add">add</a> | <a href="./users/login">login</a><br />
                        <a href="./visitas">visitas</a> | <a href="./visitas/view/1">view</a> | <a href="./visitas/edit/1">edit</a> | <a href="./visitas/add">add</a><br />
                    </p>

                    
                    <div class="row">
                        <div class="column">
                            <h4>Environment</h4>
                            <ul>
                            <?php if (version_compare(PHP_VERSION, '8.1.0', '>=')) : ?>
                                <li class="bullet success">Your version of PHP is 8.1.0 or higher (detected <?= PHP_VERSION ?>).</li>
                            <?php else : ?>
                                <li class="bullet problem">Your version of PHP is too low. You need PHP 8.1.0 or higher to use CakePHP (detected <?= PHP_VERSION ?>).</li>
                            <?php endif; ?>
    
                            <?php if (extension_loaded('mbstring')) : ?>
                                <li class="bullet success">Your version of PHP has the mbstring extension loaded.</li>
                            <?php else : ?>
                                <li class="bullet problem">Your version of PHP does NOT have the mbstring extension loaded.</li>
                            <?php endif; ?>
    
                            <?php if (extension_loaded('openssl')) : ?>
                                <li class="bullet success">Your version of PHP has the openssl extension loaded.</li>
                            <?php elseif (extension_loaded('mcrypt')) : ?>
                                <li class="bullet success">Your version of PHP has the mcrypt extension loaded.</li>
                            <?php else : ?>
                                <li class="bullet problem">Your version of PHP does NOT have the openssl or mcrypt extension loaded.</li>
                            <?php endif; ?>
    
                            <?php if (extension_loaded('intl')) : ?>
                                <li class="bullet success">Your version of PHP has the intl extension loaded.</li>
                            <?php else : ?>
                                <li class="bullet problem">Your version of PHP does NOT have the intl extension loaded.</li>
                            <?php endif; ?>
    
                            <?php if (ini_get('zend.assertions') !== '1') : ?>
                                <li class="bullet problem">You should set <code>zend.assertions</code> to <code>1</code> in your <code>php.ini</code> for your development environment.</li>
                            <?php endif; ?>
                            </ul>
                        </div>
                        <div class="column">
                            <h4>Filesystem</h4>
                            <ul>
                            <?php if (is_writable(TMP)) : ?>
                                <li class="bullet success">Your tmp directory is writable.</li>
                            <?php else : ?>
                                <li class="bullet problem">Your tmp directory is NOT writable.</li>
                            <?php endif; ?>
    
                            <?php if (is_writable(LOGS)) : ?>
                                <li class="bullet success">Your logs directory is writable.</li>
                            <?php else : ?>
                                <li class="bullet problem">Your logs directory is NOT writable.</li>
                            <?php endif; ?>
    
                            <?php $settings = Cache::getConfig('_cake_core_'); ?>
                            <?php if (!empty($settings)) : ?>
                                <li class="bullet success">The <em><?= h($settings['className']) ?></em> is being used for core caching. To change the config edit config/app.php</li>
                            <?php else : ?>
                                <li class="bullet problem">Your cache is NOT working. Please check the settings in config/app.php</li>
                            <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column">
                            <h4>Database</h4>
                            <?php
                            $result = $checkConnection('default');
                            ?>
                            <ul>
                            <?php if ($result['connected']) : ?>
                                <li class="bullet success">CakePHP is able to connect to the database.</li>
                            <?php else : ?>
                                <li class="bullet problem">CakePHP is NOT able to connect to the database.<br /><?= h($result['error']) ?></li>
                            <?php endif; ?>
                            </ul>
                        </div>
                        <div class="column">
                            <h4>DebugKit</h4>
                            <ul>
                            <?php if (Plugin::isLoaded('DebugKit')) : ?>
                                <li class="bullet success">DebugKit is loaded.</li>
                                <?php
                                $result = $checkConnection('debug_kit');
                                ?>
                                <?php if ($result['connected']) : ?>
                                    <li class="bullet success">DebugKit can connect to the database.</li>
                                <?php else : ?>
                                    <li class="bullet problem">There are configuration problems present which need to be fixed:<br /><?= $result['error'] ?></li>
                                <?php endif; ?>
                            <?php else : ?>
                                <li class="bullet problem">DebugKit is <strong>not</strong> loaded.</li>
                            <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column links">
                            <h3>Getting Started</h3>
                            <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/">CakePHP Documentation</a>
                            <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/tutorials-and-examples/cms/installation.html">The 20 min CMS Tutorial</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column links">
                            <h3>Help and Bug Reports</h3>
                            <a target="_blank" rel="noopener" href="https://slack-invite.cakephp.org/">Slack</a>
                            <a target="_blank" rel="noopener" href="https://github.com/cakephp/cakephp/issues">CakePHP Issues</a>
                            <a target="_blank" rel="noopener" href="https://discourse.cakephp.org/">CakePHP Forum</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column links">
                            <h3>Docs and Downloads</h3>
                            <a target="_blank" rel="noopener" href="https://api.cakephp.org/">CakePHP API</a>
                            <a target="_blank" rel="noopener" href="https://bakery.cakephp.org">The Bakery</a>
                            <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/">CakePHP Documentation</a>
                            <a target="_blank" rel="noopener" href="https://plugins.cakephp.org">CakePHP plugins repo</a>
                            <a target="_blank" rel="noopener" href="https://github.com/cakephp/">CakePHP Code</a>
                            <a target="_blank" rel="noopener" href="https://github.com/FriendsOfCake/awesome-cakephp">CakePHP Awesome List</a>
                            <a target="_blank" rel="noopener" href="https://www.cakephp.org">CakePHP</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column links">
                            <h3>Training and Certification</h3>
                            <a target="_blank" rel="noopener" href="https://cakefoundation.org/">Cake Software Foundation</a>
                            <a target="_blank" rel="noopener" href="https://training.cakephp.org/">CakePHP Training</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="column links">
                            <h3>PHP info</h3>
                            <?= $this->Html->link(__('PHP info'), ['..', 'phpinfo.php']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php endif; ?>
    </div>
    <?= $this->element('footer'); ?>
</body>
</html>
