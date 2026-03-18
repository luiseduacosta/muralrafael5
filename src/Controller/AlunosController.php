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
        
        $alunos = $this->paginate($query, [
            'sortableFields' => [
                'id', 'nome', 'registro', 'email', 'telefone', 'celular', 'cpf', 'nascimento', 'ingresso', 'turno'
            ]
        ]);
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
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $contained = [
            'Estagiarios' => ['Instituicoes', 'Supervisores', 'Professores'], 
            'Inscricoes' => ['Muralestagios' => ['Instituicoes']], 
            'Users'
        ];

        if (empty($id)) {
            // Give a chance with the registro
            $registro = $this->request->getQuery('registro');
            if ($registro) {
                $aluno = $this->Alunos->find()->where(['Alunos.registro' => $registro])->first();
                if ($aluno) {
                    $id = $aluno->id;
                }
            }
            if (empty($id)) {
                $this->Flash->error(__('Sem parâmetros para localizar o(a) aluno(a)'));
                return $this->redirectBack(['controller' => 'Alunos', 'action' => 'index']);
            }
        }

        try {
            $aluno = $this->Alunos->get($id, ['contain' => $contained]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Aluno não encontrado.'));
            return $this->redirectBack(['action' => 'index']);
        }

        try {
            $this->Authorization->authorize($aluno);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirectBack('/');
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
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        if ($user_data['aluno_id'] > 0) {
            $this->Flash->warning(__('Aluno já está cadastrado.'));
            return $this->redirect(['action' => 'view', $user_data['aluno_id']]);
        }

        $aluno = $this->Alunos->newEmptyEntity();
        
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->warning('Sem permissão para adicionar aluno.');
            return $this->redirect('/');
        }
        
        if ($this->request->is('post')) {
            $aluno = $this->Alunos->patchEntity($aluno, $this->request->getData());
            if ($user_data['aluno_id']) {
                $aluno->user_id = $user_data['id'];
            }
            
            if ($this->Alunos->save($aluno)) {
                $this->Flash->success(__('O aluno foi adicionado com sucesso.'));
                // Update user record with aluno_id and numero only if the user is a aluno
                if ($user_data['aluno_id']) {
                    $user = $this->fetchTable('Users')->get($aluno->user_id);
                    $user->aluno_id = $aluno->id;
                    $user->numero = $aluno->registro;
                    $this->fetchTable('Users')->save($user);
                    // Refresh the user identity in the session
                    $this->Authentication->setIdentity($user);
                }
                // Go to aluno view page
                return $this->redirect(['action' => 'view', $aluno->id]);
            }
            $this->Flash->error(__('Erro ao adicionar: não foi possível salvar os dados.'));
        }
        
        if ($user_data['aluno_id']) {   
            $email = $user_data['email'];
            $registro = $user_data['numero'];
            $aluno->email = $email;
            $aluno->registro = $registro;
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
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirectBack('/');
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
            $this->Flash->error(__('Erro ao Excluir: O aluno tem estagiários associados.'));
            return $this->redirect(['controller' => 'alunos', 'action' => 'view', $id]);
        }

        try {
            $this->Authorization->authorize($aluno);
            if ($this->Alunos->delete($aluno)) {
                $this->Flash->success(__('O aluno foi deletado com sucesso.'));
            } else {
                $this->Flash->error(__('Erro ao Excluir: Não foi possível Excluir o aluno.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__('Erro de autorização: ' . $error->getMessage()));
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
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $nome = $this->getRequest()->getQuery('nome');
        if ($nome) {
            $condition = ['Alunos.nome LIKE' => '%' . $nome . '%'];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca, [
                'sortableFields' => ['registro', 'nome', 'cpf', 'email']
            ]);
            $this->set(compact('alunos'));
            return;
        }

        $dre = $this->getRequest()->getQuery('dre');
        if ($dre) {
            $condition = ['Alunos.registro' => $dre];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca, [
                'sortableFields' => ['registro', 'nome', 'cpf', 'email']
            ]);
            $this->set(compact('alunos'));
            return;
        }
                
        $cpf = $this->getRequest()->getQuery('cpf');
        if ($cpf) {
            $condition = ['Alunos.cpf' => $cpf];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca, [
                'sortableFields' => ['registro', 'nome', 'cpf', 'email']
            ]);
            $this->set(compact('alunos'));
            return;
        }
        
        $email = $this->getRequest()->getQuery('email');
        if ($email) {
            $condition = ['Users.email' => $email];
            $busca = $this->Alunos->find('all',  ['conditions' => $condition ])->contain(['Users']);
            $alunos = $this->paginate($busca, [
                'sortableFields' => ['registro', 'nome', 'cpf', 'email']
            ]);
            $this->set(compact('alunos'));
            return;
        }
    }

   /**
     * Declaracaoperiodo method
     *
     * @param string|null $id Aluno id.
     */
    public function declaracaoperiodo($id = null)
    {
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }
        
        $this->Authorization->authorize($this->Alunos);

        $totalperiodos = $this->request->getQuery('totalperiodos');
        $novoperiodo = $this->request->getQuery('novoperiodo');

        if ($user_data && $user_data['aluno_id']) {
            $id = $user_data['aluno_id'];
        }

        if ($id == null) {
            $this->Flash->error(__("Operação não pode ser realizada porque o 'id' não foi informado."));
            return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
        }

        $aluno = $this->Alunos->get($id);

        try {
            $this->Authorization->authorize($aluno);
        } catch (ForbiddenException $e) {
            $this->Flash->error(__('Acesso não autorizado.'));
            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        // Incomplete field ingresso on record of alunos
        if (strlen($aluno->ingresso) < 6) {
            $this->Flash->error(__('Período de ingresso incompleto.'));
            return $this->redirect(['action' => 'view', $id]);
        }

        $periodo_atual = $this->configuracao->periodo_calendario_academico;

        if ($novoperiodo) {
            $periodo_inicial = $novoperiodo;
        } else {
            $periodo_inicial = $aluno->ingresso;
        }

        $inicial = explode('-', $periodo_inicial);
        $atual = explode('-', $periodo_atual);
        $semestres = ($atual[0] - $inicial[0] + 1) * 2;

        $totalperiodos = $semestres; // Simplified fallback
        if ($inicial[1] == 1 && $atual[1] == 2) {
            $totalperiodos = $semestres;
        }
        if ($inicial[1] == 1 && $atual[1] == 1) {
            $totalperiodos = $semestres - 1;
        }
        if ($inicial[1] == 2 && $atual[1] == 2) {
            $totalperiodos = $semestres - 1;
        }
        if ($inicial[1] == 2 && $atual[1] == 1) {
            $totalperiodos = $semestres - 2;
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $novoperiodo = $data['novoperiodo'] ?? $aluno->ingresso;

            // Recalculate logic...
            return $this->redirect([
                'action' => 'declaracaoperiodo',
                $id,
                '?' => ['totalperiodos' => $totalperiodos, 'novoperiodo' => $novoperiodo],
            ]);
        }

        $this->set(compact('aluno', 'totalperiodos', 'novoperiodo'));
    }

    /**
     * Gera o PDF do certificado de período do aluno.
     *
     * @param string|null $id
     * @return void
     */
    public function declaracaoperiodopdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $id = $this->request->getQuery('id');
        $totalperiodos = $this->request->getQuery('totalperiodos');

        if ($user_data && $user_data['aluno_id']) {
            $id = $user_data['aluno_id'];
        }

        if ($id === null) {
            $this->Flash->error(__("Operação não pode ser realizada porque o 'id' não foi informado."));

            return $this->redirect(['action' => 'index']);
        }

        $aluno = $this->Alunos->get($id);

        try {
            $this->Authorization->authorize($aluno);
        } catch (ForbiddenException $e) {
            $this->Flash->error(__('Acesso não autorizado.'));

            return $this->redirect(['action' => 'index']);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
            'download' => true,
            'filename' => 'declaracao_de_periodo_' . $id . '.pdf',
        ]);

        $this->set(compact('aluno', 'totalperiodos'));
    }

    /**
     * Cargahoraria method
     *
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function cargahoraria()
    {
        try {
            $this->Authorization->authorize($this->Alunos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirectBack('/');
        }
        
        $alunos = $this->Alunos->find()->contain(['Estagiarios']);

        $this->set('alunos', $this->paginate($alunos, [
            'sortableFields' => ['nome', 'registro']
        ]));
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
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirectBack('/');
        }
    
        $periodo = $this->request->getQuery('periodo') ?? $this->request->getData('periodo') ?? $this->configuracao->periodo_calendario_academico;
        $this->set('periodo', $periodo);
        
        /* lista de periodos */
        $periodototal = $this->Alunos->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, ['all' => 'Todos']);
        $periodos = array_reverse($periodos);
        $this->set('periodos', $periodos);
        
        /* Se o periodo não veio anexo como parametro então o período é o último da lista dos períodos */
        if (empty($periodo)) {
            $periodo = end($periodos);
        }

        $contained = ['Alunos', 'Instituicoes', 'Supervisores', 'Professores'];
        
        $selected = ['Estagiarios.periodo', 'Alunos.id', 'Alunos.nome', 'Instituicoes.id', 'Instituicoes.instituicao', 'Instituicoes.cep', 'Instituicoes.endereco', 'Instituicoes.bairro', 'Supervisores.nome', 'Supervisores.cress', 'Professores.nome'];

        if ($periodo === 'all') {
            $cress = $this->Alunos->Estagiarios->find()
                    ->contain($contained)
                    ->select($selected);
        } else {
            $cress = $this->Alunos->Estagiarios->find()
                    ->contain($contained)
                    ->select($selected)
                    ->where(['Estagiarios.periodo' => $periodo]);
        }

        $this->set('cress', $this->paginate($cress, [
            'sortableFields' => [
                'Alunos.nome',
                'Instituicoes.instituicao',
                'Supervisores.nome',
                'Professores.nome'
            ],
            'order' => ['Alunos.nome' => 'asc']
        ]));
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
            $this->Flash->error('Erro de autorização: ' . $error->getMessage());
            return $this->redirect('/');
        }
        
        $periodototal = $this->Alunos->Estagiarios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, ['all' => 'Todos']);
        $periodos = array_reverse($periodos);
        $this->set('periodos', $periodos);

        $periodo = $this->request->getQuery('periodo') ?? $this->request->getData('periodo') ?? $this->configuracao->termo_compromisso_periodo;
        if (empty($periodo)) {
            $periodo = end($periodos);
        }
        $this->set('periodo', $periodo);

        $contained = ['Alunos', 'Instituicoes'];

        $selected = [
            'Alunos.id',
            'Alunos.nome',
            'Alunos.cpf',
            'Alunos.nascimento',
            'Alunos.registro',
            'Estagiarios.nivel',
            'Estagiarios.periodo',
            'Estagiarios.ajuste2020',
            'Instituicoes.id',
            'Instituicoes.instituicao'
        ];

        if ($periodo === 'all') {
            $seguro = $this->Alunos->Estagiarios->find()
                ->contain($contained)
                ->select($selected);
        } else {
            $seguro = $this->Alunos->Estagiarios->find()
                ->contain($contained)
                ->where(['Estagiarios.periodo' => $periodo])
                ->select($selected);
        }

        $this->set('seguro', $this->paginate($seguro, [
            'sortableFields' => [
                'Alunos.nome',
                'Alunos.cpf',
                'Alunos.nascimento',
                'Alunos.registro',
                'nivel',
                'periodo',
                'Instituicoes.instituicao'
            ],
            'order' => ['nivel' => 'asc']
        ]));

        $instituicao = $this->fetchTable("Configuracoes")->find()->first()['instituicao_curso'];
        if (empty($instituicao)) { $instituicao = 'ESS/UFRJ'; }
        $this->set('instituicao', $instituicao);
    
    }
}