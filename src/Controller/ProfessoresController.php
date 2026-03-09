<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;
use Cake\ORM\Query;

/**
 * Professores Controller
 *
 * @property \App\Model\Table\ProfessoresTable $Professores
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfessoresController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Professores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $professores = $this->paginate($this->Professores->find('all', [
            'contain' => ['Users'],
        ]));
        $this->set(compact('professores'));
    }

    /**
     * View method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $professor = $this->Professores->get($id, [
            'contain' => ['Users', 'Estagiarios' => ['Instituicoes']],
        ]);

        try {
            $this->Authorization->authorize($professor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $this->paginate = [
            'Estagiarios' => ['limit' => 5, 'scope' => 'estagiario'],
        ];

        $estagiarios = $this->paginate($this->Professores->Estagiarios->find('all', [
            'contain' => ['Alunos', 'Instituicoes', 'Supervisores', 'Turmas'],
        ])->innerJoinWith('Professores', function (Query $query) use ($professor) {
            return $query->where([
                'professor_id' => $professor->id,
            ]);
        }));

        $this->set(compact('professor', 'estagiarios'));
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
            $this->Authorization->authorize($this->Professores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $professor = $this->Professores->newEmptyEntity();
        if ($this->request->is('post')) {
            $professor = $this->Professores->patchEntity($professor, $this->request->getData());

            if (!$professor->user_id) {
                $user = $this->Authentication->getIdentity();
                $professor->user_id = $user->get('id');
            }

            if ($this->Professores->save($professor)) {
                $this->Flash->success(__('The professor has been saved.'));
                // Update the user with the professor_id if the atual user is professor
                if ($user_data['categoria'] == '3') {
                    $user = $this->fetchTable('Users')->get($professor->user_id);
                    $user->professor_id = $professor->id;
                    $user->numero = $professor->siape;
                    $this->fetchTable('Users')->save($user);
                    // Refresh the user identity in the session
                    $this->Authentication->setIdentity($user);
                }
                // Got to view
                return $this->redirect(['action' => 'view', $professor->id]);
            }
            $this->Flash->error(__('The professor could not be saved. Please, try again.'));
        }

        if ($user_data['categoria'] == '3') {
            $email = $user_data['email'];
            $siape = $user_data['numero'];
            $professor->email = $email;
            $professor->siape = $siape;
        }
        $this->set(compact('professor'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $professor = $this->Professores->get($id);

        try {
            $this->Authorization->authorize($professor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $professor = $this->Professores->patchEntity($professor, $this->request->getData());
            if ($this->Professores->save($professor)) {
                $this->Flash->success(__('The professor has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The professor could not be saved. Please, try again.'));
        }
        $this->set(compact('professor'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $professor = $this->Professores->get($id, ['contain' => 'Estagiarios']);

        try {
            $this->Authorization->authorize($professor);
            if (sizeof($professor->estagiarios) > 0) {
                $this->Flash->warning(__("O(a) professor(a) tem estagiários associados."));
                return $this->redirect(['controller' => 'Professores', 'action' => 'view', $id]);
            }

            if ($this->Professores->delete($professor)) {
                $this->Flash->success(__('The professor has been deleted.'));
            } else {
                $this->Flash->error(__('The professor could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__('Authorization error: ' . $error->getMessage()));
        }

        return $this->redirect(['action' => 'index']);
    }
}
