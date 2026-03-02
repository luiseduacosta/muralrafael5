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
        try {
            $this->Authorization->authorize($this->Avaliacoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $avaliacoes = null;
        $estagiarios = null;

        if ($user_data['administrador_id']) {
            $query = $this->Avaliacoes->find()->contain(['Estagiarios' => ['Alunos', 'Instituicoes']]);
            $avaliacoes = $this->paginate($query, ['sortableFields' => ['id', 'Estagiarios.Alunos.nome', 'Estagiarios.Instituicoes.instituicao', 'avaliacao1', 'timestamp']]);
        } elseif ($user_data['aluno_id']) {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            if ($estagiario_id) {
                $registro = $this->Avaliacoes->Estagiarios->find()
                    ->where(['Estagiarios.id' => $estagiario_id])
                    ->first();

                $estagiariostabela = $this->fetchTable('Estagiarios');
                $query = $estagiariostabela->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.registro' => $registro->registro]);

                $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
            } else {
                $this->Flash->error(__('Selecionar estagiário, período e nível de estágio a ser avaliado'));

                return $this->redirect('/avaliacoes');
            }
        } elseif ($user_data['professor_id']) {
            $estagiariostabela = $this->fetchTable('Estagiarios');
            $query = $estagiariostabela->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                ->where(['Estagiarios.professor_id' => $user_data['professor_id']]);

            $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
        } elseif ($user_data['supervisor_id']) {
            $estagiariostabela = $this->fetchTable('Estagiarios');
            $query = $estagiariostabela->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                ->where(['Estagiarios.supervisor_id' => $user_data['supervisor_id']]);

            $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
        }

        // Initialize empty paginated results if still null to prevent template crashes
        if ($user_data['administrador_id'] && is_null($avaliacoes)) {
            $avaliacoes = $this->paginate($this->Avaliacoes);
        }
        if (!$user_data['administrador_id'] && is_null($estagiarios)) {
            $estagiarios = $this->paginate($this->fetchTable('Estagiarios'));
        }

        $this->set(compact('avaliacoes', 'estagiarios'));
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

        if ($avaliacao) {
            try {
                $this->Authorization->authorize($avaliacao);
            } catch (ForbiddenException $error) {
                $this->Flash->error('Authorization error: ' . $error->getMessage());

                return $this->redirect('/');
            }
            $this->set(compact('avaliacao'));
        } else {
            $this->Flash->error(__('Aluno sem avaliaçao'));
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

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
        try {
            $this->Authorization->authorize($this->Avaliacoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

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

        try {
            $this->Authorization->authorize($avaliacao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }
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

        try {
            $this->Authorization->authorize($avaliacao);
            if ($this->Avaliacoes->delete($avaliacao)) {
                $this->Flash->success(__('Avaliacao excluida.'));
            } else {
                $this->Flash->error(__('Avaliacao nao foi excluida. Tente novamente.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
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

        $this->set('estagiarios', $estagiarios);
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
