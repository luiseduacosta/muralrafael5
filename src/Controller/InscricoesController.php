<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Inscricoes Controller
 *
 * @property \App\Model\Table\InscricoesTable $Inscricoes
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InscricoesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $periodo = $this->getRequest()->getQuery('periodo');

        if (empty($periodo)) {
            $configuracao = $this->fetchTable('Configuracoes');
            $periodo_atual = $configuracao->find()->select(['mural_periodo_atual'])->first();
            $periodo = $periodo_atual->mural_periodo_atual;
        }
        
        $estagiariotabela = $this->fetchTable('Estagiarios');
        $periodototal = $estagiariotabela->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo',
            'order' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();

        $query = $this->Inscricoes->find()
                ->contain(['Alunos', 'Muralestagios' => ['Instituicoes']])
                ->where(['Inscricoes.periodo' => $periodo]);

        $inscricoes = $this->paginate($query);

        $this->set(compact('inscricoes', 'periodos', 'periodo'));
    }

    /**
     * View method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $inscricao = $this->Inscricoes->get($id, [
            'contain' => ['Alunos', 'Muralestagios' => ['Instituicoes']],
        ]);

        $this->set(compact('inscricao'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $dados = $this->request->getData();
        
        $periodo = $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $dados['periodo'] = $periodo;
        
        $mural_estagio_id = $id;
        if (!$mural_estagio_id) {
            $this->Flash->error(__('Erro no identificador do mural de estagios'));
        } else {
            $mural_estagio = $this->fetchTable('Muralestagios')->get($mural_estagio_id);
            $dados['mural_estagio'] = $mural_estagio;
            $dados['mural_estagio_id'] = $mural_estagio->id;
            
            /** Verifico o periodo do mural e comparo com o periodo da inscricao */
            if ($mural_estagio->periodo != $periodo) {
                $this->Flash->error(__('O periodo de inscricao nao coincide com o periodo do Mural.'));
                return $this->redirect(['controller' => 'Muralestagios', 'action' => 'view', $mural_estagio_id]);
            }

            $instituicao = $this->fetchTable('Instituicoes')->get($mural_estagio->instituicao_id);
        }

        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }
        
        $aluno = $this->fetchTable('Alunos')->get($user_data['aluno_id']);
        if (!$aluno) {
            $this->Flash->error(__('Erro ao selecionar aluno'));
            
            if ($user_data['administrador_id']) {
                return $this->redirect(['controller' => 'Users', 'action' => 'alternarusuario']);
            } else {
                $user_id = $this->Authentication->getIdentifier();
                return $this->redirect(['controller' => 'Users', 'action' => 'view', $user_id]);
            }
            
        } else {  
            $dados['registro'] = $aluno->registro;
            $dados['aluno_id'] = $aluno->id;
            
            /** Verifico se já fez inscrição para não duplicar */
            $inscricao_duplicada = $this->Inscricoes->find()->where(['Inscricoes.aluno_id' => $aluno->id, 'Inscricoes.mural_estagio_id' => $mural_estagio->id])->first();
            if ($inscricao_duplicada) {
                $this->Flash->error(__("Inscrição já realizada"));
                return $this->redirect(['controller' => 'Inscricoes', 'action' => 'view', $inscricao_duplicada->id]);
            }
        }
        
        $data = date('Y-m-d');
        $dados['data'] = $data;

        $inscricao = $this->Inscricoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $inscricao = $this->Inscricoes->patchEntity($inscricao, $dados);
            if ($this->Inscricoes->save($inscricao)) {
                $this->Flash->success(__('Inscricao realizada com sucesso.'));

                return $this->redirect(['action' => 'view', $inscricao->id]);
            }
            $this->Flash->error(__('The inscricao could not be saved. Please, try again.'));
        }
        $this->set(compact('inscricao', 'aluno', 'periodo', 'mural_estagio', 'data', 'instituicao'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $inscricao = $this->Inscricoes->get($id, [
            'contain' => ['Alunos'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $inscricao = $this->Inscricoes->patchEntity($inscricao, $this->request->getData());
            if ($this->Inscricoes->save($inscricao)) {
                $this->Flash->success(__('The inscricao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The inscricao could not be saved. Please, try again.'));
        }
        $this->set(compact('inscricao'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inscricao = $this->Inscricoes->get($id);
        if ($this->Inscricoes->delete($inscricao)) {
            $this->Flash->success(__('The inscricao has been deleted.'));
        } else {
            $this->Flash->error(__('The inscricao could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Termocompromisso method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Renders termocompromisso
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function termocompromisso($id = null) 
    {

        $registro = $this->getRequest()->getQuery('registro');

        $alunosTable = $this->fetchTable("Alunos");
        if (empty($registro) && !empty($id)) { 
            $aluno = $alunosTable->get($id);
            $registro = $aluno->registro; 
        }
            
        // pr($registro);
        // die("termocompromisso");

        /* Captura o periodo de estagio para o termo de compromisso */
        $periodo = $this->fetchTable("Configuracoes")->find()->first()['termo_compromisso_periodo'];

        /* Busca em estagiarios o ultimo estagio do aluno */
        $estagiario = $this->fetchTable("Estagiarios")->find('all', [
            'conditions' => ['Estagiarios.registro IS' => $registro]
        ])->first();
        // pr($estagiario);
        // die('estagiario');

        /* Capturo os dados do aluno na tabela alunos */
        if (empty($aluno)) {
            $aluno = $this->fetchTable("Alunos")->find('all', [
                'conditions' => ['Alunos.registro IS' => $registro]
            ])->first();
        }
        // pr($aluno);
        // die('aluno');
        
        /* Se nao encontra nada entao eh um aluno que ainda nao eh estagiario, portanto vai cursar estagio I */
        if (empty($estagiario)) {
            /* Aluno sem estágios registrados. Inícia estágio I */
            $nivel_ultimo = 1; // Nivel eh 1
            $inserir = 0; // Inserir estagiário novo no nível I
        } 
        
        if (empty($registro)) { $this->Flash->error(__("Registro não encontrado")); }
        else if (empty($aluno)) {
            $this->Flash->error(__("Aluno não cadastrado"));
            $this->redirect(['controller' => 'Alunos', 'action' => 'add?registro=' . $registro]);
        }
        
        $reg = (string)$registro;
        //pr($reg);
        //die();
        
        /* Calculo o ano de ingresso para definir se é do ajuste2020 */
        if (strlen(trim($reg)) == 9) {
            /* Se o aluno ingressou depois de 2019 entao são tres niveis de estagio, senao son 4 niveis */
            /* O calculo deveria ser realizado com o campo de inscricao e nao a partir do registro */
            if (intval(substr(trim($reg), 1, 2)) > 19) {
                // echo 'estudante ingressou em 2020 ou depois';
                $estagiario['ajuste2020'] = 1;
                $ultimo_nivel_curricular = 3;
            } else {
                $estagiario['ajuste2020'] = 0;
                $ultimo_nivel_curricular = 4;
            }
        } elseif (strlen(trim($reg)) == 8) {
            /* Alunos anteriores ao ano de 2000 */
            $estagiario['ajuste2020'] = 0;
            $ultimo_nivel_curricular = 4;
        }
        // die();
        $nivel_ultimo = null;
        /* Ultimo periodo cadastrado é menor que periodo atual então tem que cadastrar novo estágio */
        if ($estagiario['periodo'] < $periodo) {

            $estagiario['id'] = null;
            $inserir = 0; // Inserir

            /* Se o nivel de estagio a ser cadastrado eh menor que o ultimo nivel curricular entao aumento o nivel de estagio para o seguinte nivel */
            if ($estagiario['nivel'] < $ultimo_nivel_curricular) {
                $nivel_ultimo = $estagiario['nivel'] + 1;
                // die("Inserir novo estágio");
            /* Caso contrario, ou seja, se o nivel a ser cadastrado supera o nivel curricular, entao o aluno ja finalizou estagio curricular e agora esta fazendo estagio nao obrigatorio */    
            } elseif ($estagiario['nivel'] >= $ultimo_nivel_curricular) {
                $nivel_ultimo = 9; // estágio não obrigatório
                // die("Inserir novo estágio não obrigatório");
            }
            // pr($nivel_ultimo);
            // die();
            /* Se o periodo cadastrado é igual ao periodo atual então o aluno está solicitando novamente o mesmo termo de compromisso */
        } elseif ($estagiario['periodo'] == $periodo) {
            $nivel_ultimo = $estagiario['nivel'];
            $inserir = 1; // Atualizar estagiario
            // die("Atualizar estágio");
        } else {
            $this->Flash->error(__("Período atual é menor que período de estágio cadastrado. Verifique os dados."));
            //$this->redirect(['controller' => 'inscricaos', 'action' => 'termosolicita']);
        }
        
        /* Capturo as instituicoes */
        $instituicoesTable = $this->fetchTable("Instituicoes");
        $instituicoes = $instituicoesTable->find(
                'list',
                [
                    'fields' => ['Instituicoes.id', 'Instituicoes.instituicao'],
                    'order' => 'Instituicoes.instituicao',
                ]
        );
        // pr($instituicoes);

        /* Capturo os supervisores da instituicao atual */
        if (isset($estagiario['instituicao_id'])) {
            $supervisores = $instituicoesTable->find('all',
                [
                    //'contain' => ['Supervisor' => ['order' => 'nome']],
                    'conditions' => ['Instituicoes.id' => $estagiario['instituicao_id']]
                ]
            );

            foreach ($supervisores as $supervisor) {
                $supervisoresAtuais[$supervisor['id']] = $supervisor['nome'];
                // pr($supervisor['nome']);
            }
        }
        
        // Envio os dados
        $this->set('estagiario_id', $estagiario['id']);
        $this->set('inserir', $inserir);
        $this->set('nivel', $nivel_ultimo);
        $this->set('ingresso', isset($estagiario['ingresso']) ? $estagiario['ingresso'] : (isset($aluno['ingresso']) ? $aluno['ingresso'] : null));
        $this->set('alunoturno', isset($estagiario['turno']) ? $estagiario['turno'] : (isset($aluno['turno']) ? $aluno['turno'] : null));
        $this->set('aluno_id', isset($estagiario['id_aluno']) ? $estagiario['id_aluno'] : null);
        $this->set('registro', $registro);
        $this->set('aluno', isset($estagiario['nome']) ? $estagiario['nome'] : (isset($aluno['nome']) ? $aluno['nome'] : null));
        $this->set('turno', isset($turno_ultimo) ? $turno_ultimo : 'I');
        $this->set('periodo', $periodo);
        // $this->set('id_area', $id_area);
        $this->set('complemento_id', isset($estagiario['complemento_id']) ? $estagiario['complemento_id'] : null);
        //$this->set('alunos_id', isset($estagiario'id']) ? $estagiario['id'] : '');
        $this->set('ajuste2020', isset($estagiario['ajuste2020']) ? $estagiario['ajuste2020'] : 0);

        $this->set('professor_atual', isset($estagiario['id_professor']) ? $estagiario['id_professor'] : 0);
        $this->set('instituicao_atual', isset($estagiario['id_instituicao']) ? $estagiario['id_instituicao'] : 0);
        $this->set('supervisor_atual', isset($estagiario['id_supervisor']) ? $estagiario['id_supervisor'] : 0);

        $this->set('instituicoes', $instituicoes);
        $this->set('supervisores', isset($super_atuais) ? $super_atuais : null); // Aluno sem estaǵio não tem supervisores de instituição cadastrados
    }
    
}
