<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;
use Exception;

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
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Administradores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

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
    public function view(?string $id = null)
    {
        $administrador = null;

        if ($id) {
            try {
                $administrador = $this->Administradores->get($id, [
                    'contain' => ['Users'],
                ]);
            } catch (Exception $error) {
                $this->Flash->error('Error: ' . $error->getMessage());

                return $this->redirect('/');
            }
        } else {
            $user_id = $this->request->getQuery('user_id');
            if ($user_id) {
                try {
                    $administrador = $this->Administradores->find('all', [
                        'conditions' => ['Administradores.user_id' => $user_id],
                        'contain' => ['Users'],
                    ])->first();
                } catch (Exception $error) {
                    $this->Flash->error('Error: ' . $error->getMessage());

                    return $this->redirect('/');
                }
            }
        }

        if (!$administrador) {
            $this->Flash->error('Administrador not found');

            return $this->redirect(['action' => 'index']);
        }

        try {
            $this->Authorization->authorize($administrador);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $this->set(compact('administrador'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Administrador id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $administrador = $this->Administradores->get($id);

        try {
            $this->Authorization->authorize($administrador);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

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

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $administrador = $this->Administradores->newEmptyEntity();
        if ($this->request->is('post')) {
            $administrador = $this->Administradores->patchEntity($administrador, $this->request->getData());

            if ($this->Administradores->save($administrador)) {
                $this->Flash->success(__('The administrador has been saved.'));

                return $this->redirect(['action' => 'view', $administrador->id]);
            }
            $this->Flash->error(__('The administrador could not be saved. Please, try again.'));
        }
        $this->set(compact('administrador'));
    }
}
