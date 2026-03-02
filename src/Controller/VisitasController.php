<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;

/**
 * Visitas Controller
 *
 * @property \App\Model\Table\VisitasTable $Visitas
 * @method \App\Model\Entity\Visita[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VisitasController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Visitas);
        //$this->paginate = [
        //    'contain' => ['Instituicoes'],
        //];
        $visitas = $this->paginate($this->Visitas->find()->contain(['Instituicoes']));

        $this->set(compact('visitas'));
    }

    /**
     * View method
     *
     * @param string|null $id Visita id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        try {
        $visita = $this->Visitas->get($id, [
            'contain' => ['Instituicoes'],
        ]);
        } catch (RecordNotFoundException $e) {
            throw new ForbiddenException('A visita que você tentou visualizar não existe.');
        }
        $this->Authorization->authorize($visita);

        $this->set(compact('visita'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->authorize($this->Visitas);
        $visita = $this->Visitas->newEmptyEntity();
        if ($this->request->is('post')) {
            $visita = $this->Visitas->patchEntity($visita, $this->request->getData());
            if ($this->Visitas->save($visita)) {
                $this->Flash->success(__('The visita has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The visita could not be saved. Please, try again.'));
        }
        $instituicoes = $this->Visitas->Instituicoes->find('list');
        $this->set(compact('visita', 'instituicoes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Visita id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $visita = $this->Visitas->get($id, [
            'contain' => [],
        ]);
        $this->Authorization->authorize($visita);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $visita = $this->Visitas->patchEntity($visita, $this->request->getData());
            if ($this->Visitas->save($visita)) {
                $this->Flash->success(__('The visita has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The visita could not be saved. Please, try again.'));
        }
        $instituicoes = $this->Visitas->Instituicoes->find('list');
        $this->set(compact('visita', 'instituicoes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Visita id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $visita = $this->Visitas->get($id);
        $this->Authorization->authorize($visita);
        if ($this->Visitas->delete($visita)) {
            $this->Flash->success(__('The visita has been deleted.'));
        } else {
            $this->Flash->error(__('The visita could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
