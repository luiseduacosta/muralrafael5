<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;
/**
 * Turnos Controller
 *
 * @property \App\Model\Table\TurnosTable $Turnos
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class TurnosController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        try {
            $this->Authorization->authorize($this->Turnos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
    }
    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authorization.Authorization');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $turnos = $this->paginate($this->Turnos);

        $this->set(compact('turnos'));
    }

    /**
     * View method
     *
     * @param string|null $id Turno id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $turno = $this->Turnos->get($id);
        $this->set(compact('turno'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $turno = $this->Turnos->newEmptyEntity();
        if ($this->request->is('post')) {
            $turno = $this->Turnos->patchEntity($turno, $this->request->getData());
            if ($this->Turnos->save($turno)) {
                $this->Flash->success(__('The turno has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The turno could not be saved. Please, try again.'));
        }
        $this->set(compact('turno'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Turno id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $turno = $this->Turnos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $turno = $this->Turnos->patchEntity($turno, $this->request->getData());
            if ($this->Turnos->save($turno)) {
                $this->Flash->success(__('The turno has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The turno could not be saved. Please, try again.'));
        }
        $this->set(compact('turno'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Turno id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $turno = $this->Turnos->get($id);
        if ($this->Turnos->delete($turno)) {
            $this->Flash->success(__('The turno has been deleted.'));
        } else {
            $this->Flash->error(__('The turno could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
