<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Alunos Controller
 *
 * @property \App\Model\Table\AlunosTable $Alunos
 * @method \App\Model\Entity\Aluno[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AlunosController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Alunos);
            $query = $this->Alunos->find('all')->contain(['Users']);
        } catch (ForbiddenException $error) {
            $query = $this->Authorization->applyScope($this->Alunos->find('all')->contain(['Users']));
        }
        
        $alunos = $this->paginate($query);
        $this->set(compact('alunos'));
    }

    /**
     * View method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contained = [
            'Estagiarios' => ['Instituicoes', 'Supervisores', 'Professores', 'Turmas'], 
            'Inscricoes' => ['Muralestagios' => ['Instituicoes']], 
            'Users'
        ];

        $aluno = $this->Alunos->get($id, [ 'contain' => $contained ]);

        try {
            $this->Authorization->authorize($aluno);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $this->set(compact('aluno'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $aluno = $this->Alunos->newEmptyEntity();
        
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->warning('Erro de authorização: aluno já está cadastrado');
            return $this->redirect('/');
        }
        
        if ($this->request->is('post')) {
            $aluno = $this->Alunos->patchEntity($aluno, $this->request->getData());
            
            if (!$aluno->user_id) { 
                $user = $this->Authentication->getIdentity();
                $aluno->user_id = $user->get('id'); 
            }
            
            if ($this->Alunos->save($aluno)) {
                $this->Flash->success(__('O aluno foi adicionado com sucesso.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erro ao adicionar: não foi possível salvar os dados.'));
        }
        $this->set(compact('aluno'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $aluno = $this->Alunos->get($id);
        
        try {
            $this->Authorization->authorize($aluno);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $aluno = $this->Alunos->patchEntity($aluno, $this->request->getData());
            if ($this->Alunos->save($aluno)) {
                $this->Flash->success(__('A edição foi salva com sucesso.'));
                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('Erro ao salvar: não foi possível salvar os dados.'));
        }
        $this->set(compact('aluno'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $aluno = $this->Alunos->get($id, ['contain' => ['Estagiarios']]);
        
        if (sizeof($aluno->estagiarios) > 0) {
            $this->Flash->error(__('Erro ao deletar: O aluno tem estagiários associados.'));
            return $this->redirect(['controller' => 'alunos', 'action' => 'view', $id]);
        }

        try {
            $this->Authorization->authorize($aluno);
            if ($this->Alunos->delete($aluno)) {
                $this->Flash->success(__('O aluno foi deletado com sucesso.'));
            } else {
                $this->Flash->error(__('Erro ao deletar: Não foi possível deletar o aluno.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__( 'Erro de authorização: ' . $error->getMessage() ));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * Busca method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function busca() 
    {
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $nome = $this->getRequest()->getQuery('nome');
        if ($nome) {
            $condition = ['Alunos.nome LIKE' => '%' . $nome . '%'];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca);
            $this->set(compact('alunos'));
            return;
        }

        $dre = $this->getRequest()->getQuery('dre');
        if ($dre) {
            $condition = ['Alunos.registro' => $dre];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca);
            $this->set(compact('alunos'));
            return;
        }
                
        $cpf = $this->getRequest()->getQuery('cpf');
        if ($cpf) {
            $condition = ['Alunos.cpf' => $cpf];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca);
            $this->set(compact('alunos'));
            return;
        }
        
        $email = $this->getRequest()->getQuery('email');
        if ($email) {
            $condition = ['Users.email' => $email];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca);
            $this->set(compact('alunos'));
            return;
        }
    }
    
    /**
     * Planilhacress method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function planilhacress($id = null) 
    {
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $periodo = $this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $this->set('periodo', $periodo);
        
        /* lista de periodos */
        $periodototal = $this->Alunos->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, array('all' => 'Todos'));
        $periodos = array_reverse($periodos);
        $this->set('periodos', $periodos);
        
        /* Se o periodo não veio anexo como parametro então o período é o último da lista dos períodos */
        if (empty($periodo)) {
            $periodo = end($periodos);
        }
        // pr($periodos);

        $contained = ['Alunos', 'Instituicoes', 'Supervisores', 'Professores'];
        
        $selected = ['Estagiarios.periodo', 'Alunos.id', 'Alunos.nome', 'Instituicoes.id', 'Instituicoes.instituicao', 'Instituicoes.cep', 'Instituicoes.endereco', 'Instituicoes.bairro', 'Supervisores.nome', 'Supervisores.cress', 'Professores.nome'];

        $ordered = ['Alunos.nome'];
        
        if ($periodo === 'all') {
            //ini_set('memory_limit', '2048M');
            $cress = $this->Alunos->Estagiarios->find()
                    ->contain($contained)
                    ->select($selected)
                    ->order($ordered);
        } else {
            $cress = $this->Alunos->Estagiarios->find()
                    ->contain($contained)
                    ->select($selected)
                    ->where(['Estagiarios.periodo' => $periodo])
                    ->order($ordered);
        }

        // pr($cress);
        // die();
        $this->set('cress', $this->paginate($cress));
    }

    /**
     * Planilhaseguro method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function planilhaseguro($id = null) 
    {
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $periodo = $this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $this->set('periodo', $periodo);

        $periodototal = $this->Alunos->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, array('all' => 'Todos'));
        $periodos = array_reverse($periodos);
        $this->set('periodos', $periodos);

        if (empty($periodo)) {
            $periodo = end($periodos);
        }

        $contained = ['Alunos', 'Instituicoes'];

        $selected = [
            'Alunos.id',
            'Alunos.nome',
            'Alunos.cpf',
            'Alunos.nascimento',
            'Alunos.registro',
            'Estagiarios.nivel',
            'Estagiarios.periodo',
            'Instituicoes.id',
            'Instituicoes.instituicao'
        ];

        $ordered = ['Estagiarios.nivel'];

        if ($periodo === 'all') {
            //ini_set('memory_limit', '2048M');
            $seguro = $this->Alunos->Estagiarios->find()
                ->contain($contained)
                ->select($selected)
                ->order($ordered);
        } else {
            $seguro = $this->Alunos->Estagiarios->find()
                ->contain($contained)
                ->where(['Estagiarios.periodo' => $periodo])
                ->select($selected)
                ->order($ordered);
        }

        //if (!empty($t_seguro)) {
        //    array_multisort($criterio, SORT_ASC, $t_seguro);
        //}
        // pr($t_seguro);
        //$this->set('t_seguro', $t_seguro);
        
        $this->set('seguro', $this->paginate($seguro));

        $instituicao = $this->fetchTable("Configuracoes")->find()->first()['instituicao'];
        $this->set('instituicao', $instituicao);
        // die();
    }
    
    /**
     * Declaracaoperiodo method
     *
     * @param string|null $id Aluno id.
     */
    public function declaracaoperiodo($id = null) 
    {
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }
        
        /**
         * Autorização. Verifica se o aluno cadastrado no Users está acessando seu próprio registro.
         */
        $option = 0;


        if ($user_data['administrador_id']) {
            if ($id) $option = "id = " . $id;
        }
        
        if ($user_data['aluno_id']) {
            $option = "id = " . $user_data['aluno_id'];
        }

        /**
         * Consulto a tabela alunos com o registro ou com o id
         */
        if ($option) {
            $aluno = $this->Alunos->find()->where([$option])->first();
        }

        if (empty($aluno)) {
            $this->Flash->error(__('Erro: Não foi possível encontrar o aluno.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'view', $user_data['id']]);
            // die('Professores e Supervisores não autorizados');
        }

        /**
         * Calculo a partir do ingresso em que periodo o aluno esté neste momento.
         */
        
        /* Capturo o periodo do calendario academico atual */
        $periodo_atual = $this->fetchTable("Configuracoes")->find()->first()['periodo_calendario_academico'];

        
        /** Capturo o periodo inicial para o cálculo dos semetres.
         *  Inicialmente coincide com o campo de ingresso.
         *  Mas pode ser alterada para fazer coincider os semestres no casos dos alunos que trancaram.
         */
        $novoperiodo = $this->getRequest()->getData('novoperiodo');
        if ($novoperiodo) {
            $periodo_inicial = $this->getRequest()->getData('novoperiodo');
        } else {
            $periodo_inicial = $aluno->ingresso;
        }

        /**
         * Separo o periodo em duas partes: o ano e o indicador de primeiro ou segundo semestre.
         */

        $inicial = explode('-', $periodo_inicial);
        $atual = explode('-', $periodo_atual);
        
        // echo $atual[0] . ' ' . $inicial[0] . '<br>';
        // echo $atual[1] . ' ' . $inicial[1] . '<br>';
        // die();
        
        /**
         * Calculo o total de semestres
         */
        $semestres = (($atual[0] - $inicial[0]) + 1) * 2;
        // pr($semestres);

        /** Se começa no semestre 1 e finaliza no 2 então são anos inteiros */
        if (($inicial[1] == 1) && ($atual[1] == 2)) {
            $totalperiodos = $semestres;
        }

        /** Se começa no semestre 1 e finaliza no 1 então perdeu um semestre (o segundo semestre atual) */
        if (($inicial[1] == 1) && ($atual[1] == 1)) {
            $totalperiodos = $semestres - 1;
        }

        /** Se começa no semestre 2 e finaliza no 2 então perdeu um semestre (o primeiro semestre inicial) */
        if (($inicial[1] == 2) && ($atual[1] == 2)) {
            $totalperiodos = $semestres - 1;
        }

        /** Se começa no semestre 2 e finaliza no semestre 1 então perdeu dois semestres (o primeiro do ano inicial e o segundo do ano atual) */
        if (($inicial[1] == 2) && ($atual[1] == 1)) {
            $totalperiodos = $semestres - 2;
        }

        /** Se o período inicial é maior que o período atual então informar que há um erro */
        if ($totalperiodos <= 0) {
            $this->Flash->error(__('Erro: período inicial é maior que período atual.'));
        }


        if ($totalperiodos > 20) {
            $this->Flash->error(__('Erro: período é maior q o permitido.'));
        }

        // pr($totalperiodos);
        // die();
        
        if (isset($this->getRequest()->getData()['novoperiodo'])) {
            $aluno->periodonovo = $this->getRequest()->getData()['novoperiodo'];
        } else {
            $aluno->periodonovo = $aluno->ingresso;
        }

        // pr($aluno);
        // pr($aluno->turno);
        // die();
        $this->set('aluno', $aluno);
        $this->set('totalperiodos', $totalperiodos);
    }

    /**
     * Declaracaoperiodopdf method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function declaracaoperiodopdf($id = NULL) {

        $this->layout = false;
        $id = $this->getRequest()->getQuery('id');
        $totalperiodos = $this->getRequest()->getQuery('totalperiodos');

        if (is_null($id)) {
            $this->cakeError('error404');
        } else {
            $aluno = $this->Alunos->find()
                    ->contain([])
                    ->where(['Alunos.id' => $id])
                    ->first();
        }
        // pr($id);
        // pr($totalperiodos);
        // pr($aluno);
        // die('aluno');

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
                'pdfConfig',
                [
                    'orientation' => 'portrait',
                    //'download' => true, // This can be omitted if "filename" is specified.
                    //'filename' => 'declaracao_de_periodo_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
                ]
        );

        $this->set('aluno', $aluno);
        $this->set('totalperiodos', $totalperiodos);
    }

    /**
     * Cargahoraria method
     *
     * @param string|null $id Aluno id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function cargahoraria($ordem = null) {

        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de authorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $alunos = $this->Alunos->find()->contain(['Estagiarios']);

        $this->set('alunos', $this->paginate($alunos));
    }

    /**
     * Cargahoraria method
     *
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */    
    public function folhasolicita()
    {
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }
        
        $registro = $this->getRequest()->getQuery('registro');
        //pr('reg: ' . $registro);

        if (empty($registro)) {
            if (!$user_data['administrador_id']) $this->Flash->info(__('Erro: os dados não foram encontrados'));
        } else {
            $this->redirect(['action' => 'folhadeatividadespdf', $registro, 'ext' => 'pdf', 'folhadeatividades']);
            
        } 
    }
    
}
