<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Turnos Controller
 *
 * @property \App\Model\Table\TurnosTable $Turnos
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class TurnosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Turnos);
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
    public function view(?string $id = null)
    {
        $turno = $this->Turnos->get($id);
        $this->Authorization->authorize($turno);
        $this->set(compact('turno'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->authorize($this->Turnos);
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
    public function edit(?string $id = null)
    {
        $turno = $this->Turnos->get($id, contain: []);
        $this->Authorization->authorize($turno);
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
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $turno = $this->Turnos->get($id);
        $this->Authorization->authorize($turno);
        if ($this->Turnos->delete($turno)) {
            $this->Flash->success(__('The turno has been deleted.'));
        } else {
            $this->Flash->error(__('The turno could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
