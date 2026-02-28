<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Estagiarios Controller
 *
 * @property \App\Model\Table\EstagiariosTable $Estagiarios
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EstagiariosController extends AppController
{
    /**
     * paginate array
     */
    protected array $paginate = [
        'sortableFields' => [
            'id', 'Alunos.nome', 'registro', 'Turnos.turno', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'Professores.nome'
        ]
    ];
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $periodo = $this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $this->set('periodo', $periodo);
        
        $contained = ['Alunos', 'Professores', 'Supervisores', 'Instituicoes', 'Turnos', 'Turmas'];

        $conditions = ['conditions' => ['Estagiarios.periodo' => $periodo] ];

        try {
            $this->Authorization->authorize($this->Estagiarios);
            if ($periodo == 'all') {
                $estagiarios = $this->Estagiarios->find('all')->contain($contained);
            } else {
                $estagiarios = $this->Estagiarios->find('all', $conditions)->contain($contained);
            }
        } catch (ForbiddenException $error) {
            if ($periodo == 'all') {
                $estagiarios = $this->Authorization->applyScope($this->Estagiarios->find('all')->contain($contained));
            } else {
                $estagiarios = $this->Authorization->applyScope($this->Estagiarios->find('all', $conditions)->contain($contained));
            }
        }
        
        $this->set('estagiarios', $this->paginate($estagiarios));

        
        $periodototal = $this->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, array('all' => 'Todos'));
        $periodos = array_reverse($periodos);
        
        $this->set('periodos', $periodos);
    }

    /**
     * View method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $estagiario = $this->Estagiarios->get($id, [
            'contain' => ['Alunos', 'Instituicoes', 'Supervisores', 'Professores', 'Turmas', 'Turnos', 'Complementos'/*, 'Folhadeatividades' */],
        ]);

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        //$folhadeatividades = $this->Estagiarios->Folhadeatividades->find()
        //    ->where(['estagiario_id' => $id])
        //    ->all();
        
        $this->set(compact('estagiario'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $estagiario = $this->Estagiarios->newEmptyEntity();

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }

        $configuracao = $this->fetchTable('Configuracoes');
        $periodo_atual = $configuracao->find()->select(['mural_periodo_atual'])->first();
        
        $periodo = $periodo_atual->mural_periodo_atual;
        
        if ($this->request->is('post')) {
            $estagiario = $this->Estagiarios->patchEntity($estagiario, $this->request->getData());
            if ($this->Estagiarios->save($estagiario)) {
                $this->Flash->success(__('The estagiario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The estagiario could not be saved. Please, try again.'));
        }
        
        $alunos = $this->Estagiarios->Alunos->find('list');
        $instituicoes = $this->Estagiarios->Instituicoes->find('list');
        $supervisores = $this->Estagiarios->Supervisores->find('list');
        $professores = $this->Estagiarios->Professores->find('list');
        $turmas = $this->Estagiarios->Turmas->find('list');
        $turnos = $this->Estagiarios->Turnos->find('list');
        
        $this->set(compact('periodo', 'estagiario', 'alunos', 'instituicoes', 'supervisores', 'professores', 'turmas', 'turnos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $estagiario = $this->Estagiarios->get($id);
        
        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $estagiario = $this->Estagiarios->patchEntity($estagiario, $this->request->getData());
            if ($this->Estagiarios->save($estagiario)) {
                $this->Flash->success(__('The estagiario has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The estagiario could not be saved. Please, try again.'));
        }
        
        $alunos = $this->Estagiarios->Alunos->find('list');
        $instituicoes = $this->Estagiarios->Instituicoes->find('list');
        $supervisores = $this->Estagiarios->Supervisores->find('list');
        $professores = $this->Estagiarios->Professores->find('list');
        $turmas = $this->Estagiarios->Turmas->find('list');
        $turnos = $this->Estagiarios->Turnos->find('list');
        
        $this->set(compact('estagiario', 'alunos', 'instituicoes', 'supervisores', 'professores', 'turmas', 'turnos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $estagiario = $this->Estagiarios->get($id);

        try {
            $this->Authorization->authorize($estagiario);
            if ($this->Estagiarios->delete($estagiario)) {
                $this->Flash->success(__('The estagiario has been deleted.'));
            } else {
                $this->Flash->error(__('The estagiario could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__( 'Authorization error: ' . $error->getMessage() ));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * pdf test
     *
     *
    public function pdf($id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false); 
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        //$this->viewBuilder()->setOption('pdfConfig',[
            //'orientation' => 'portrait',
            //'download' => false,
            //'filename' => 'Filename_' . $id . '.pdf'
        //]);
        
        $estagiario = $this->Estagiarios->get($id);
        $this->set('estagiario', $estagiario);
    }
    */
    
    /**
     * Termodecompromisso method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     *
     */
    public function termodecompromisso($id = null)
    {

        $aluno_id = $this->getRequest()->getQuery('aluno_id');
        $instituicao_id = $this->getRequest()->getQuery('instituicao_id');

        /** Vou utilizar a tabela alunos */
        $estudantestabela = $this->fetchTable('Alunos');

        if ($this->getRequest()->is('post')) {

            /** Guardo a informação do formulário para atualizar ou inserir um novo registro */
            $dadosinsere = $this->getRequest()->getData();

            /** Tem que ter uma instituição */
            if (empty($dadosinsere['instituicao_id'])) {
                if ($this->getRequest()->getQuery('instituicao_id')) {
                    $dadosinsere['instituicao_id'] = $this->getRequest()->getQuery('instituicao_id');
                } else {
                    $this->Flash->error(__('Selecione uma instituição de estágio.'));
                    return $this->redirect(['action' => 'termodecompromisso']);
                }
            }

            /** Verifica que se não tem supervisor selecionado o valor seja nulo */
            if (empty($dadosinsere['supervisor_id'])) {
                $dadosinsere['supervisor_id'] = null;
            }

            /** Se tem o id é uma atualização, senão é uma inserção de um novo registro de estágio */
            if ($this->getRequest()->getData('id')) {
                $estagiario = $this->Estagiarios->get($this->getRequest()->getData('id'), [
                    'contain' => [],
                ]);
            } else {
                $estagiario = $this->Estagiarios->newEmptyEntity();
                /** Capturo o id do aluno para passar para o estagiario */
                $aluno = $estudantestabela->find()
                    ->contain([])
                    ->where(['Alunos.id' => $aluno_id])
                    ->first();
                // pr($aluno);
                // die();
                $dadosinsere['aluno_id'] = $aluno->id;
            }

            /** Atualiza ou insere o registro em Estagiarios. */
            $estagiarioresultado = $this->Estagiarios->patchEntity($estagiario, $dadosinsere);
            if ($this->Estagiarios->save($estagiarioresultado)) {

                $this->Flash->success(__('Estágio criado ou atualizado.'));
                /** Gravo o cookie estagiario_id para que o menu superior fique com o submenu_aluno */
                $this->getRequest()->getSession()->write('estagiario_id', $estagiarioresultado->id);

                /** Insere os dados do aluno na tabela alunos */
                $this->alunoinsere($aluno_id, $estagiarioresultado->id);

                return $this->redirect(['action' => 'termodecompromisso', '?' => ['aluno_id' => $estagiarioresultado->aluno_id, 'instituicao_id' => $estagiarioresultado->instituicao_id]]);
            } else {
                // debug($estagiarioresultado);
                $this->Flash->error(__('Não foi possível inserir ou atualizar o estagiario. Tente mais uma vez.'));
            }
        } // Finaliza post

        /** Calculo o periodo atual para estimar o nivel de estágio do Termo de Compromisso. */
        if (!isset($periodoatual) || empty($periodoatual)) {
            $configuracaotabela = $this->fetchTable('Configuracoes');
            $periodo = $configuracaotabela->find()->first();
            $periodoatual = $periodo->mural_periodo_atual;
        }

        /** A partir do Id do aluno obtenho o último nivel de estágio do aluno estagiário e calculo o próximo nível */
        if (!isset($aluno_id)) {
            if (isset($id)) {
                $estagiariotabela = $this->fetchTable('Estagiarios');
                // pr($estagiariotabela);
                $estagiario = $estagiariotabela->find()
                    ->where([['id' => $id]])
                    ->select(['aluno_id'])
                    ->first();
                $aluno_id = $estagiario->aluno_id;
            } else {
                $this->Flash->error(__('Sem parâmetros para fazer o Termo de Compromisso'));
                return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
            }
        }

        if ($aluno_id) {

            /** Capturo o último estágio do estagiário */
            $ultimoestagio = $this->Estagiarios->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores'])
                ->where(['Estagiarios.aluno_id' => $aluno_id])
                ->order(['Estagiarios.nivel' => 'desc'])
                ->first();

            /** Último estágio do estagiario */
            if ($ultimoestagio) {

                /** Vai para a funcao nivelsestagio() para calcular o nivel de estágio */
                $nivel = $this->nivelestagio($periodoatual, $ultimoestagio);

                /** Verifico se e uma atualizacao ou uma nova insercao */
                if ($periodoatual == $ultimoestagio->periodo) {
                    $this->set('atualiza', 1); // o estagiario sera atualizado
                } elseif ($periodoatual > $ultimoestagio->periodo) {
                    $this->set('atualiza', 0); // nova inserção de estagiario
                } else {
                    $this->Flash->error(__('Periodo atual menor que ultimo periodo de estagio cadastrado.'));
                    return $this->redirect(['controller' => 'Estagiarios', 'action' => 'view', $ultimoestagio->id]);
                }
                $this->set('ultimoestagio', $ultimoestagio);
                /** Nao estagiario */
            } else {
                /** Aluno sem estágio: nível 1 */
                $nivel = 1;
                $estudante_semestagio = $estudantestabela->find()
                    ->contain([])
                    ->where(['Alunos.id' => $aluno_id])
                    ->select(['id', 'registro', 'nome', 'ingresso'])
                    ->first();
                $this->set('estudante_semestagio', $estudante_semestagio);
                $this->set('atualiza', 0); // nova inserção de estagiario
            }

            $this->set('nivel', $nivel);
            $this->set('periodo', $periodoatual);

            /** Seleciona os supervisores da instituição. Primeiro precisa do instituicao_id */
            if (!isset($instituicao_id)) {
                if (isset($ultimoestagio)) {
                    $instituicao_id = $ultimoestagio->instituicao->id;
                    // echo $instituicao_id;
                }
            } else {
                $this->set('supervisores', 'Sem dados');
            }
            if (isset($instituicao_id)) {

                $supervisoresinstituicao = $this->selecionasupervisores($instituicao_id);
                if (isset($supervisoresinstituicao)) {
                    $this->set('supervisores', $supervisoresinstituicao);
                } else {
                    $this->set('supervisores', 'Sem dados');
                }
            }

            $this->set('aluno_id', $aluno_id);
            $this->set('instituicao_id', $instituicao_id);

            $aluno = $estudantestabela->find()->where(['Alunos.id' => $aluno_id])->first();
            $instituicoes = $this->Estagiarios->Instituicoes->find('list');
            $turmas = $this->Estagiarios->Turmas->find('list');
            $this->set(compact('instituicoes', 'turmas', 'aluno'));
            if (isset($supervisoresinstituicao)):
                $this->set('supervisoresdainstituicao', $supervisoresinstituicao);
            endif;
        }
    }
    
    /**
     * Selecionasupervisores method
     *
     * Seleciona os supervisores da instituicao
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     *
     */
    private function selecionasupervisores($instituicao_id = null)
    {
        $supervisoresinstituicao = null;
        if ($instituicao_id) {

            $supervisoresDaInstituicao = $this->Estagiarios->Instituicoes->find()
                ->contain(['Supervisores'])
                ->where(['Instituicoes.id' => $instituicao_id])
                ->first();

            // pr($supervisoresDaInstituicao);
            // die();

            if ($supervisoresDaInstituicao) {

                foreach ($supervisoresDaInstituicao->supervisores as $supervisor) {
                    $supervisoresinstituicao[$supervisor['id']] = $supervisor['nome'];
                }
            } else {
                $supervisoresinstituicao[0] = "Sem supervisor(a)";
                $supervisoresinstituicao[0] = "Sem dados";
            }
        }
        return $supervisoresinstituicao;
    }

    /**
     * Nivelestagio method
     *
     * Compara o periodoautal com o periodo de estagio do estagiario para definiar o nivel de estagio
     *
     */
    private function nivelestagio($periodoatual, $ultimoestagio)
    {
        /* Se o periodo atual é o mesmo do periodo cadastrado no estagiário deixa o nivel como está */
        if ($periodoatual == $ultimoestagio->periodo) {
            $nivel = $ultimoestagio->nivel;
            /** Se o periodo atual é maior que o cadastrado então passa para o próximo nivel e insere um novo registro */
        } elseif ($periodoatual > $ultimoestagio->periodo) {
            $nivel = $ultimoestagio->nivel + 1;
            /** Calculo o ultimo nível de estágio possível a partir do ajuste curricular. */
            /** Se nivel é maior que o ultimo nivel curricular então está realizando estagio extracurricular e o nivel é 9. */
            if ($nivel > 4) {
                // Estágio não curricular
                $nivel = 9;
            }
        } else {
            $this->Flash->error(__("Período de estágio atual não pode ser menor que o último período cursado."));
            return $this->redirect(['action' => 'termodecompromisso', $ultimoestagio->id]);
        }
        return $nivel;
    }

    /**
     * Alunoinsere method
     *
     * Inserir dados de aluno na tabela alunoestagiario.
     *
     */
    private function alunoinsere($aluno_id, $estagiario_id)
    {
        /**
         * Verificar se o aluno está cadastrado em estagiarios.
         */
        $estagiario = $this->Estagiarios->find()->where(['id' => $estagiario_id])->first();
        $alunostabela = $this->fetchTable('Alunoestagiarios');
        $alunoestagiario = $alunostabela->find()->where(['registro' => $estagiario->registro])->first();
        if ($alunoestagiario) {
            echo 'Aluno cadastrado: atualizar apenas o aluno_id em estagiarios' . '<br>';
            $estagiario_entity = $this->Estagiarios->get($estagiario_id);
            $estagiario = $this->Estagiarios->find()->where(['id' => $estagiario_id])->first();
            // pr($estagiario);
            $dadosinsere = $estagiario->toArray();
            // pr($dadosinsere);
            // die('estagiario');
            $dadosinsere['aluno_id'] = $alunoestagiario->id;
            // pr($dadosinsere);
            // die();
            $estagiario_aluno_resultado = $this->Estagiarios->patchEntity($estagiario_entity, $dadosinsere);
            if ($this->Estagiarios->save($estagiario_aluno_resultado)) {
                $this->Flash->success(__("Estagiário atualizado"));
            } else {
                // debug($estagiario_aluno_resultado);
                // die();
            }
        } else {
            echo 'Aluno não cadastrado: fazer uma inserção nova na tabela alunos e atualizar aluno_id na tabela estagiarios' . '<br>';
            $estudantestabela = $this->fetchTable('Alunos');
            $aluno = $estudantestabela->find()
                ->where(['Alunos.id' => $aluno_id])
                ->first()
                ->toArray();
            // pr($aluno);
            // die();
            $alunostabela = $this->fetchTable('Alunos');
            $aluno_novo = $alunostabela->newEmptyEntity();
            $aluno_novo_resultado = $alunostabela->patchEntity($aluno_novo, $aluno);
            // pr($aluno_novo_resultado);
            // die();
            if ($alunostabela->save($aluno_novo_resultado)) {
                $this->Flash->success(__('Aluno inserido'));
                /** Atualizo o aluno_id na tabela estagiarios */
                $estagiario_entity = $this->Estagiarios->get($estagiario_id);
                $estagiario = $this->Estagiarios->find()->where(['id' => $estagiario_id])->first();
                // pr($estagiario);
                $dadosinsere = $estagiario->toArray();
                // pr($dadosinsere);
                // die('estagiario');
                $dadosinsere['aluno_id'] = $aluno_novo_resultado->id;
                // pr($dadosinsere);
                // die();
                $estagiario_aluno_resultado = $this->Estagiarios->patchEntity($estagiario_entity, $dadosinsere);
                if ($this->Estagiarios->save($estagiario_aluno_resultado)) {
                    $this->Flash->success(__("Estagiário atualizado"));
                } else {
                    // debug($estagiario_aluno_resultado);
                    // die();
                }
            } else {
                // debug($aluno_novo_resultado);
                // die('Error');
            }
        }
        return;
    }

    /**
     * Termodecompromissopdf method
     *
     * Compara o periodoautal com o periodo de estagio do estagiario para definiar o nivel de estagio
     *
     * @param string|null $id Estagiario id.
     */   
    public function termodecompromissopdf($id = null)
    {

        $this->layout = false;
        if (is_null($id)) {
            $this->cakeError('error404');
        } else {
            $estagiario = $this->Estagiarios->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.id IS' => $id]);
        }
        // pr($estagiario->first());
        // die();
        $configuracaotabela = $this->fetchTable('Configuracoes');
        $configuracao = $configuracaotabela->get(1);
        // pr($configuracao);
        // die();

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                //'download' => true, // This can be omitted if "filename" is specified.
                //'filename' => 'termo_de_compromisso_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('configuracao', $configuracao);
        $this->set('estagiario', $estagiario->first());
    }

    /**
     * Selecionadeclaracaodeestagio method
     *
     * @param string|null $id Estagiario id.
     */     
    public function selecionadeclaracaodeestagio($id = null)
    {

        /* No login foi capturado o id do estagiário */
        if (is_null($id)) { $id = $this->getRequest()->getSession()->read('estagiario_id'); }
        if (is_null($id)) {
            $this->Flash->error(__('Selecionar o aluno estagiário'));
            return $this->redirect('/alunos/index');
        } else {
            $estagiarios = $this->Estagiarios->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.id IS' => $id]);
            //pr($estagiario);
            // die();
        }

        $this->set('estagiarios', $this->paginate($estagiarios));
    }

    /**
     * Declaracaodeestagiopdf method
     *
     * @param string|null $id Estagiario id.
     */   
    public function declaracaodeestagiopdf($id = null)
    {

        $estagiarioquery = $this->Estagiarios->find()
            ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
            ->where(['Estagiarios.id IS' => $id])
            ->first();
        // pr($estagioquery);
        // die('estagioquery');

        if (!$estagiarioquery) {
            $this->Flash->error(__('Sem estagio cadastrado.'));
            return $this->redirect(['controller' => 'estagiarios', 'action' => 'view', $id]);
        }

        if (empty($estagiarioquery->aluno->identidade)) {
            $this->Flash->error(__("Aluno sem RG"));
            return $this->redirect('/alunos/view/' . $estagiarioquery->aluno->id);
        }

        if (empty($estagiarioquery->aluno->orgao)) {
            $this->Flash->error(__("Aluno não especifica o orgão emisor do documento"));
            return $this->redirect('/alunos/view/' . $estagiarioquery->aluno->id);
        }
        if (empty($estagiarioquery->aluno->cpf)) {
            $this->Flash->error(__("Aluno sem CPF"));
            return $this->redirect('/alunos/view/' . $estagiarioquery->aluno->id);
        }

        if (empty($estagiarioquery->supervisor->id)) {
            $this->Flash->error(__("Falta o supervisor de estágio"));
            return $this->redirect('/estagiarios/view/' . $estagiarioquery->id);
        }

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                //'download' => true, // This can be omitted if "filename" is specified.
                //'filename' => 'declaracao_de_estagio_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('estagiario', $estagiarioquery);
    }
    
    /**
     * Selecionafolhadeatividades method
     *
     * @param string|null $id Estagiario id.
     */  
    public function selecionafolhadeatividades($id = null)
    {

        /* No login foi capturado o id do estagiário */
        $id = $this->getRequest()->getSession()->read('estagiario_id');
        if (is_null($id)) {
            $this->Flash->error(__('Selecionar o aluno e o estágio'));
            return $this->redirect('/alunos/index');
        } else {
            $estagiarios = $this->Estagiarios->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.registro' => $this->getRequest()->getSession()->read('registro')])
                ->all();
            //pr($estagiario);
            // die();
        }

        $this->set('estagiarios', $estagiarios);
    }

    /**
     * Folhadeatividadespdf method
     *
     * @param string|null $id Estagiario id.
     */  
    public function folhadeatividadespdf($id = null)
    {

        $this->layout = false;
        if (is_null($id)) {
            $this->Flash->error(__('Por favor selecionar o estágio do aluno'));
            return $this->redirect('/alunos/view?registro=' . $this->getRequest()->getSession()->read('registro'));
        } else {
            $estagiario = $this->Estagiarios->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes', 'Professores'])
                ->where(['Estagiarios.id IS' => $id])
                ->first();
        }
        // pr($estagiario);
        // die('estagioario');

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                //'download' => true, // This can be omitted if "filename" is specified.
                //'filename' => 'folha_de_atividades_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('estagiario', $estagiario);
    }

    /**
     * Selecionaavaliacaodiscente method
     *
     * @param string|null $id Estagiario id.
     */      
    public function selecionaavaliacaodiscente($id = null)
    {

        /* No login foi capturado o id do estagiário */
        $id = $this->getRequest()->getSession()->read('estagiario_id');
        if (is_null($id)) {
            $this->Flash->error(__('Selecionar o aluno estagiário'));
            return $this->redirect('/alunos/index');
        } else {
            $estagiarios = $this->Estagiarios->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.registro' => $this->getRequest()->getSession()->read('registro')])
                ->all();
            //pr($estagiario);
            // die();
        }

        $this->set('estagiarios', $estagiarios);
    }

    /**
     * Avaliacaodiscentepdf method
     *
     * @param string|null $id Estagiario id.
     */  
    public function avaliacaodiscentepdf($id = null)
    {

        $this->layout = false;
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

        $estagiario = $this->Estagiarios->find()
            ->contain(['Alunos', 'Supervisores', 'Instituicoes', 'Professores'])
            ->where(['Estagiarios.id IS' => $estagiario_id])
            ->first();

        if (!$estagiario) {
            $this->Flash->error(__('Sem estagiarios cadastrados'));
            return $this->redirect(['estagiario' => $estagiario, 'action' => 'view', $estagiario_id]);
        }

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                //'download' => true, // This can be omitted if "filename" is specified.
                //'filename' => 'avaliacao_discente_' . $estagiario_id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('estagiario', $estagiario);
    }
    
    /**
     * Lancanota method
     *
     * @param string|null $id Estagiario id.
     */  
    public function lancanota($id = null)
    {
        $siape = $this->getRequest()->getQuery('siape');
          
        $periodo = $this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $this->set('periodo', $periodo);
        
        $periodototal = $this->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, array('all' => 'Todos'));
        $periodos = array_reverse($periodos);
        
        $this->set('periodos', $periodos);
        
        $contained = ['Alunos', 'Professores', 'Supervisores', 'Instituicoes', 'Turnos', 'Turmas'];
        
        $conditions = ['conditions' => ['Estagiarios.periodo' => $periodo] ];
        
        $estagiarios = $this->Estagiarios
            ->find('all', $conditions)
            ->contain($contained);
        //    ->where(['siape IS' => $siape])
        //    ->first();

        // pr($estagiarios);
        //foreach ($estagiarios as $estagio):
            //$folhadeatividadestabela = $this->fetchTable('Folhadeatividades');
            //$folha = $folhadeatividadestabela->find()
            //    ->where(['Folhadeatividades.estagiario_id' => $estagio->id])
            //    ->first();
            //if (isset($folha)):
            //    // pr($folha);
            //    $estagiarioslancanota[$i]['folha_id'] = $folha->id;
            //else:
            //    $estagiarioslancanota[$i]['folha_id'] = null;
            //endif;
            //if (isset($estagio->avaliacao->id)):
            //    $estagiarioslancanota[$i]['avaliacao_id'] = $estagio->avaliacao->id;
            //else:
             //   $estagiarioslancanota[$i]['avaliacao_id'] = null;
            //endif;
        // endforeach;
        // pr($estagiarioslancanota);
        // die();
        
        $this->set('estagiarios',  $this->paginate($estagiarios));

    }
    
}
