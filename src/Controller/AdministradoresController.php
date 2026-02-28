<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Administradores Controller
 *
 * @property \App\Model\Table\AdministradoresTable $Administradores
 * @method \App\Model\Entity\Administrador[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdministradoresController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        try {
            $this->Authorization->authorize($this->Administradores);
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
        $administradores = $this->paginate($this->Administradores);
        $this->set(compact('administradores'));
    }

    /**
     * View method
     *
     * @param string|null $id Administrador id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $administrador = $this->Administradores->get($id, [
            'contain' => ['Users'],
        ]);
        $this->set(compact('administrador'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Administrador id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $administrador = $this->Administradores->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $administrador = $this->Administradores->patchEntity($administrador, $this->request->getData());
            if ($this->Administradores->save($administrador)) {
                $this->Flash->success(__('The administrador has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The administrador could not be saved. Please, try again.'));
        }
        $this->set(compact('administrador'));
    }

}
