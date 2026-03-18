<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Complementos Controller
 *
 * @property \App\Model\Table\ComplementosTable $Complementos
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ComplementosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Complementos);
        $complementos = $this->paginate($this->Complementos);

        $this->set(compact('complementos'));
    }

    /**
     * View method
     *
     * @param string|null $id Complemento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $complemento = $this->Complementos->get($id);
        $this->Authorization->authorize($complemento);

        $this->set(compact('complemento'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->authorize($this->Complementos);
        $complemento = $this->Complementos->newEmptyEntity();
        if ($this->request->is('post')) {
            $complemento = $this->Complementos->patchEntity($complemento, $this->request->getData());

            if (!$complemento->user_id) {
                $user = $this->Authentication->getIdentity();
                $complemento->user_id = $user->get('id');
            }

            if ($this->Complementos->save($complemento)) {
                $this->Flash->success(__('The Complemento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Complemento could not be saved. Please, try again.'));
        }
        $this->set(compact('complemento'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Complemento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $complemento = $this->Complementos->get($id);
        $this->Authorization->authorize($complemento);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $complemento = $this->Complementos->patchEntity($complemento, $this->request->getData());
            if ($this->Complementos->save($complemento)) {
                $this->Flash->success(__('The Complemento has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The Complemento could not be saved. Please, try again.'));
        }
        $this->set(compact('complemento'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Complemento id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $complemento = $this->Complementos->get($id);
        $this->Authorization->authorize($complemento);
        if ($this->Complementos->delete($complemento)) {
            $this->Flash->success(__('The Complemento has been deleted.'));
        } else {
            $this->Flash->error(__('The Complemento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
