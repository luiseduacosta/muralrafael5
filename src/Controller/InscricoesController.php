<?php

declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;

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
        try {
            $this->Authorization->authorize($this->Inscricoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
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
        try {
            $this->Authorization->authorize($inscricao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
        $this->set(compact('inscricao'));
    }

    /**
     * Add method. O aluno e o admin podem fazer inscrição.
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $this->Authorization->skipAuthorization();
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }

        $dados = $this->request->getData();

        $periodo = $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $dados['periodo'] = $periodo;
        
        $mural_estagio_id = $this->getRequest()->getQuery("mural_estagio_id");

        if (!$mural_estagio_id) {
            $this->Flash->error(__('Erro no identificador do mural de estagios'));
            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        } else {
            $mural_estagio = $this->fetchTable('Muralestagios')->get($mural_estagio_id);
            $dados['mural_estagio'] = $mural_estagio;
            $dados['muralestagio_id'] = $mural_estagio->id;
            
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

        // Admin não pode fazer inscrição de aluno? Corrigir
        if ($user_data['categoria'] == '2') {
            $aluno = $this->fetchTable('Alunos')->get($user_data['aluno_id']);
            $dados['registro'] = $aluno->registro;
            $dados['aluno_id'] = $aluno->id;
        } else {
            $this->Flash->error(__('Selecione um aluno(a) para fazer a inscrição.'));
            return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
        }
        /** Verifico se já fez inscrição para não duplicar */
        $inscricao_duplicada = $this->Inscricoes->find()->where(['Inscricoes.aluno_id' => $aluno->id, 'Inscricoes.muralestagio_id' => $mural_estagio->id])->first();
        if ($inscricao_duplicada) {
            $this->Flash->error(__("Inscrição já realizada"));
            return $this->redirect(['controller' => 'Inscricoes', 'action' => 'view', $inscricao_duplicada->id]);
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

        try {
            $this->Authorization->authorize($inscricao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

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

        try {
            $this->Authorization->authorize($inscricao);
            if ($this->Inscricoes->delete($inscricao)) {
                $this->Flash->success(__('The inscricao has been deleted.'));
            } else {
                $this->Flash->error(__('The inscricao could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }
}
