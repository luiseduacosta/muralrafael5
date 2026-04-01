<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Exception;

/**
 * Instituicoes Controller
 *
 * @property \App\Model\Table\InstituicoesTable $Instituicoes
 * @method \App\Model\Entity\Instituicao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstituicoesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Instituicoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $busca = $this->request->getQuery('busca');
        if ($busca) {
            $condition = ['Instituicoes.instituicao LIKE' => '%' . $busca . '%'];
        }
        if (!isset($condition)) {
            $condition = [];
        }

        $query = $this->Instituicoes->find()->where($condition)->contain(['Areas']);

        $instituicoes = $this->paginate($query, [
            'order' => ['Instituicoes.instituicao' => 'ASC'],
            'sortableFields' => [
                'id',
                'Instituicoes.instituicao',
                'cnpj',
                'convenio',
                'expira',
                'email',
                'estagiarios_count',
            ],
        ]);

        $this->set(compact('instituicoes'));
    }

    /**
     * View method
     *
     * @param string|null $id Instituicaoestagio id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        // Load the main institution with only light associations
        $instituicao = $this->Instituicoes->find()->contain([
            'Areas',
            'Supervisores' => ['Users'],
        ])->where(['Instituicoes.id' => $id])->first();

        if (!$instituicao) {
            $this->Flash->error(__('Instituição não encontrada.'));

            return $this->redirect(['action' => 'index']);
        }

        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        // Paginate Estagiarios separately
        $this->paginate = [
            'Estagiarios' => ['limit' => 10, 'scope' => 'estagiario'],
            'Muralestagios' => ['limit' => 10, 'scope' => 'mural'],
            'Visitas' => ['limit' => 10, 'scope' => 'visita'],
        ];

        $estagiarios = $this->paginate($this->Instituicoes->Estagiarios->find()
            ->where(['Estagiarios.instituicao_id' => $id])
            ->contain(['Alunos', 'Professores', 'Supervisores']));

        $muralestagios = $this->paginate($this->Instituicoes->Muralestagios->find()
            ->where(['Muralestagios.instituicao_id' => $id]));

        $visitas = $this->paginate($this->Instituicoes->Visitas->find()
            ->where(['Visitas.instituicao_id' => $id]));

        $this->set(compact('instituicao', 'estagiarios', 'muralestagios', 'visitas'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        try {
            $this->Authorization->authorize($this->Instituicoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $instituicao = $this->Instituicoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->fetchTable('Areas')
            ->find('list')
            ->order(['area' => 'ASC'])
            ->select(['id', 'area'])
            ->all()
            ->toArray();
        $supervisores = $this->fetchTable('Supervisores')
            ->find('list')
            ->order(['nome' => 'ASC'])
            ->select(['id', 'nome'])
            ->all()
            ->toArray();
        $this->set(compact('instituicao', 'areas', 'supervisores'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Instituicao id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $instituicao = $this->Instituicoes->find()
            ->contain(['Supervisores'])
            ->where(['Instituicoes.id' => $id])
            ->first();
        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['controller' => 'Instituicoes', 'action' => 'view', $id]);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->fetchTable('Areas')
            ->find('list')
            ->order(['area' => 'ASC'])
            ->select(['id', 'area'])
            ->all()
            ->toArray();
        $supervisores = $this->fetchTable('Supervisores')
            ->find('list')
            ->order(['nome' => 'ASC'])
            ->select(['id', 'nome'])
            ->all()
            ->toArray();
        $this->set(compact('instituicao', 'areas', 'supervisores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Instituicao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instituicao = $this->Instituicoes->get($id, ['contain' => ['Estagiarios']]);

        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'index']);
        }

        // If the instituicao has estagiarios, show an error message and return to the view
        if (count($instituicao->estagiarios) > 0) {
            $this->Flash->error(__('Erro ao Excluir: A instituicao tem estagiários associados.'));

            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'view', $id]);
        }

        if ($this->Instituicoes->delete($instituicao)) {
            $this->Flash->success(__('The instituicao has been deleted.'));
        } else {
            $this->Flash->error(__('The instituicao could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * selecionasupervisores method - Ajax
     *
     * @return \Cake\Http\Response|null|void
     */
    public function selecionasupervisores()
    {
        $this->Authorization->skipAuthorization();
        if (!$this->request->is('post')) {
            return $this->response->withStatus(400);
        }

        $instituicao_id = $this->request->getData('id');
        try {
            $supervisores = $this->fetchTable('Supervisores')->find('list', [
                'keyField' => 'id',
                'valueField' => 'nome',
            ])
            ->matching('Instituicoes', function ($q) use ($instituicao_id) {
                return $q->where(['Instituicoes.id' => $instituicao_id]);
            })
            ->order(['Supervisores.nome' => 'ASC'])
            ->toArray();

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($supervisores));
        } catch (Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Erro ao buscar supervisores']));
        }
    }
}
