<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Complementos Controller
 *
 * @property \App\Model\Table\ComplementosTable $Complementos
 * @method \App\Model\Entity\Complemento[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ComplementosController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        try {
            $this->Authorization->authorize($this->Complementos);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect('/');
        }
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    
    public function index()
    {
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
    public function view($id = null)
    {
        $complemento = $this->Complementos->get($id);
        
        $this->set(compact('complemento'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $complemento = $this->Complementos->newEmptyEntity();
        if ($this->request->is('post')) {
            $complemento = $this->Complementos->patchEntity($complemento, $this->request->getData());

            if (!$Complemento->user_id) { 
                $user = $this->Authentication->getIdentity();
                $Complemento->user_id = $user->get('id'); 
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
    public function edit($id = null)
    {
        $complemento = $this->Complementos->get($id);
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
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $complemento = $this->Complementos->get($id);
        if ($this->Complementos->delete($complemento)) {
            $this->Flash->success(__('The Complemento has been deleted.'));
        } else {
            $this->Flash->error(__('The Complemento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
