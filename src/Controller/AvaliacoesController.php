<?php

declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;
use App\Model\Table\AvaliacoesTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;
use function Cake\I18n\__;

/**
 * Avaliacoes Controller
 *
 * @property AvaliacoesTable $Avaliacoes
 * @method \App\Model\Entity\Avaliaco[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class AvaliacoesController extends AppController
{
    /**
     * paginate array
     */
    protected array $paginate = [
        'sortableFields' => ['id', 'timestamp']
    ];
    /**
     * Index method. Mostra os estágios de um aluno estagiario.
     *
     * @return Response|null|void Renders view
     */
    public function index($id = NULL)
    {
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }
        
        if ($user_data['administrador_id']) {
            $avaliacoes = $this->paginate($this->Avaliacoes->find()->contain(['Estagiarios' => ['Alunos', 'Instituicoes']]));
            $this->set(compact('avaliacoes'));
        }
        else if ($user_data['aluno_id']) {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            // pr($estagiario_id);
            // die();
            if ($estagiario_id) {
                $registro = $this->Avaliacoes->Estagiarios->find()
                    ->where(['Estagiarios.id' => $estagiario_id])
                    ->first();
                // pr($registro);
                // die();
                $estagiariostabela = $this->fetchTable('Estagiarios');
                $estagiarios = $estagiariostabela->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.registro' => $registro->registro])
                    ->all();
                // pr($estagiarios);
                // die();
                $this->set('estagiarios', $estagiarios);
            } else {
                $this->Flash->error(__('Selecionar estagiário, período e nível de estágio a ser avaliado'));
                return $this->redirect('/avaliacoes');
            }
        }
    }

    /**
     * Supervisoravaliacao method
     *
     * @return Response|null|void Renders view
     */
    public function supervisoravaliacao($id = NULL)
    {

        /* O submenu_navegacao envia o cress */
        $cress = $this->getRequest()->getQuery('cress');
        if (is_null($cress)) {
            $this->Flash->error(__('Selecionar estagiário, período e nível de estágio a ser avaliado'));
            return $this->redirect('/alunos/view?registro=' . $this->getRequest()->getSession()->read('registro'));
        } else {
            $estagiarios = $this->Avaliacoes->Estagiarios->find()
                ->contain(['Supervisores', 'Alunos', 'Professores', 'Folhadeatividades'])
                ->where(['Supervisores.cress' => $cress])
                ->order(['periodo' => 'desc'])
                ->all();
            // pr($estagiario);
            $this->set('estagiarios', $estagiarios);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Avaliaco id.
     * @param mixed $estagiario_id
     * @return Response|null|void Renders view
     * @throws RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contained = ['Estagiarios' => ['Alunos', 'Instituicoes', 'Professores', 'Supervisores']];
        
        if ($id) {
            $avaliacao = $this->Avaliacoes->find()->contain($contained)
                ->where(['Avaliacoes.id' => $id])
                ->first();
        } else {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            $avaliacao = $this->Avaliacoes->find()->contain($contained)
                ->where(['Avaliacoes.estagiario_id' => $estagiario_id])
                ->first();
        }
        // pr($avaliacao);
        // die();
        if ($avaliacao) {
            $this->set(compact('avaliacao'));
        } else {
            /** Somente supervisor e administrador (?) podem avaliar. Portanto, redireciona para ver o estágio do estágiario */
            $this->Flash->error(__('Aluno sem avaliaçao'));
            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'view', $estagiario_id]);
        }
    }

    /**
     * Add method
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = NULL)
    {

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        $avaliacaoexiste = null;
        
        if ($estagiario_id) {
            $avaliacaoexiste = $this->Avaliacoes->find()
                ->where(['estagiario_id' => $estagiario_id])
                ->first();
        } elseif ($id) {
            $avaliacaoexiste = $this->Avaliacoes->find()
                ->where(['id' => $id])
                ->first();            
        }// else {
            //$this->Flash->error(__('Faltam parâmetros de identificação da avaliação'));
            //return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
        //}

        if ($avaliacaoexiste) {
            $this->Flash->error(__('Estagiário já foi avaliado'));
            // return $this->redirect(['controller' => 'avaliacoes', 'action' => 'view', $avaliacaoexiste->id]);
        }
        // pr($this->request->getData());
        // die();
        $avaliacao = $this->Avaliacoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $avaliacaoresposta = $this->Avaliacoes->patchEntity($avaliacao, $this->request->getData());
            // pr($avaliacao);
            // die();
            if ($this->Avaliacoes->save($avaliacaoresposta)) {
                $this->Flash->success(__('Avaliação registrada.'));

                return $this->redirect(['controller' => 'avaliacoes', 'action' => 'index', $this->getRequest()->getData('estagiario_id')]);
            }
            $this->Flash->error(__('Avaliaçãoo no foi registrada. Tente novamente.'));
        }

        if ($estagiario_id) {
            $estagiario = $this->Avaliacoes->Estagiarios->find()
                ->contain(['Alunos'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
            // pr($estagiario);
        } else {
            $estagiario = $this->Avaliacoes->Estagiarios->find()
            ->contain(['Alunos'])
            ->first();
        }
        
        $this->set(compact('avaliacao', 'estagiario'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Avaliaco id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $avaliacao = $this->Avaliacoes->get($id, [
            'contain' => ['Estagiarios' => 'Alunos'],
        ]);
        // pr($avaliacao->estagiario);
        $estagiario = $avaliacao->estagiario;
        // die();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $avaliacao = $this->Avaliacoes->patchEntity($avaliacao, $this->request->getData());
            if ($this->Avaliacoes->save($avaliacao)) {
                $this->Flash->success(__('Avaliacao atualizada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Avaliaçao não foi atualizada. Tente novamente.'));
            return $this->redirect(['action' => 'edit', $id]);
        }
        // $estagiarios = $this->Avaliacoes->Estagiarios->find('list');
        $this->set(compact('avaliacao', 'estagiario'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Avaliaco id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $avaliacao = $this->Avaliacoes->get($id);
        if ($this->Avaliacoes->delete($avaliacao)) {
            $this->Flash->success(__('Avaliacao excluida.'));
        } else {
            $this->Flash->error(__('Avaliacao nao foi excluida. Tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function selecionaavaliacao($id = NULL)
    {

        /* No login foi capturado o id do estagiário */
        $id = $this->getRequest()->getSession()->read('estagiario_id');
        if (is_null($id)) {
            $this->Flash->error(__('Selecionar o aluno estagiário'));
            return $this->redirect('/alunos/index');
        } else {
            $estagiariostabela = $this->fetchTable('Estagiarios');
            $estagiarios = $estagiariostabela->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.registro' => $this->getRequest()->getSession()->read('registro')])
                ->all();
        }

        $this->set('estagiarios', $this->paginate(estagiario));
    }

    public function imprimeavaliacaopdf($id = NULL)
    {

        /* No login foi capturado o id do estagiário */
        $this->layout = false;
        if (is_null($id)) {
            $this->Flash->error(__('Por favor selecionar a folha de avaliação do estágio do aluno'));
            return $this->redirect('/alunos/view?registro=' . $this->getRequest()->getSession()->read('registro'));
        } else {
            $avaliacaoquery = $this->Avaliacoes->find()
                ->contain(['Estagiarios' => ['Alunos', 'Supervisores', 'Professores', 'Instituicoes']])
                ->where(['Avaliacoes.id' => $id]);
        }
        $avaliacao = $avaliacaoquery->first();
        // pr($avaliacao);
        // die();

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true, // This can be omitted if "filename" is specified.
                'filename' => 'avaliacao_discente_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('avaliacao', $avaliacao);
    }
}
