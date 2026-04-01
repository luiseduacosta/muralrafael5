<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;

/**
 * Supervisores Controller
 *
 * @property \App\Model\Table\SupervisoresTable $Supervisores
 * @method \App\Model\Entity\Supervisor[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SupervisoresController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Supervisores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $busca = $this->request->getQuery('busca');
        if ($busca) {
            $condition = ['OR' => ['Supervisores.nome LIKE' => '%' . $busca . '%']];
        }
        if (!isset($condition)) {
            $condition = [];
        }

        $query = $this->Supervisores->find()->where($condition)->contain(['Users']);

        $supervisores = $this->paginate($query, [
            'order' => ['nome' => 'ASC'],
            'sortableFields' => [
                'id',
                'nome',
                'cpf',
                'cress',
                'email',
                'estagiarios_count',
            ],
        ]);

        $this->set(compact('supervisores'));
    }

    /**
     * View method
     *
     * @param string|null $id Supervisor id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $contained = [
            'Instituicoes' => ['Areas'],
            'Estagiarios' => ['Alunos', 'Instituicoes', 'Supervisores', 'Professores'],
            'Users',
        ];
        $this->paginate = [
            'contain' => $contained,
        ];

        if (!$id) {
            $this->Flash->info(__('Invalid supervisor id.'));
            if ($user_data['supervisor_id']) {
                $id = $user_data['supervisor_id'];
            }
        }

        if (!$id) {
            $this->Flash->error(__('Invalid supervisor id.'));

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $supervisor = $this->Supervisores->get($id, [
            'contain' => $contained,
        ]);

        try {
            $this->Authorization->authorize($supervisor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $this->set(compact('supervisor'));
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

        try {
            $this->Authorization->authorize($this->Supervisores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        if ($user_data['supervisor_id'] > 0) {
            $this->Flash->warning(__('Supervisor já está cadastrado.'));

            return $this->redirect(['action' => 'view', $user_data['supervisor_id']]);
        }

        $supervisor = $this->Supervisores->newEmptyEntity();
        if ($this->request->is('post')) {
            $supervisor = $this->Supervisores->patchEntity($supervisor, $this->request->getData());

            if ($user_data['supervisor_id']) {
                $user = $this->fetchTable('Users')->get($supervisor->user_id);
                $supervisor->user_id = $user->id;
            }

            if ($this->Supervisores->save($supervisor)) {
                $this->Flash->success(__('The supervisor has been saved.'));

                return $this->redirect(['action' => 'view', $supervisor->id]);
            }
            $this->Flash->error(__('The supervisor could not be saved. Please, try again.'));
        }
        if ($user_data['supervisor_id']) {
            $email = $user_data['email'];
            $cress = $user_data['numero'];
            $supervisor->email = $email;
            $supervisor->cress = $cress;
        }
        $instituicoes = $this->Supervisores->Instituicoes->find('list')->order(['Instituicoes.instituicao' => 'ASC']);
        $this->set(compact('supervisor', 'instituicoes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Supervisor id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $supervisor = $this->Supervisores->get($id, [
            'contain' => ['Instituicoes'],
        ]);

        try {
            $this->Authorization->authorize($supervisor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $supervisor = $this->Supervisores->patchEntity($supervisor, $this->request->getData());
            if ($this->Supervisores->save($supervisor)) {
                $this->Flash->success(__('The supervisor has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The supervisor could not be saved. Please, try again.'));
        }
        $instituicoes = $this->Supervisores->Instituicoes->find('list')->order(['Instituicoes.instituicao' => 'ASC']);
        $this->set(compact('supervisor', 'instituicoes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Supervisor id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $supervisor = $this->Supervisores->get($id, ['contain' => 'Estagiarios']);

        try {
            $this->Authorization->authorize($supervisor);

            if (count($supervisor->estagiarios) > 0) {
                $this->Flash->warning(__('Supervisor(a) tem estagiários associados'));

                return $this->redirect(['controller' => 'Supervisores', 'action' => 'view', $id]);
            }

            if ($this->Supervisores->delete($supervisor)) {
                $this->Flash->success(__('The supervisor has been deleted.'));
            } else {
                $this->Flash->error(__('The supervisor could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }
}
