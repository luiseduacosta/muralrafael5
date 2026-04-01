<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;

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
        // When calling from view and not found it with the parameter, them pass it to the add button
        $instituicao_id = $this->request->getQuery('instituicao_id');
        if (!empty($instituicao_id)) {
            $this->set('instituicao_id', $instituicao_id);
        }
        $this->Authorization->authorize($this->Visitas);

        $visitas = $this->paginate($this->Visitas->find()->contain(['Instituicoes']));

        $this->set('visitas', $visitas);
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
        if ($id == null) {
            $id = $this->request->getQuery('instituicao_id');
            if (empty($id)) {
                $this->Flash->info(__('Sem parâmetro da instituição.'));

                return $this->redirect($this->referer());
            }
        }

        try {
            $visita = $this->Visitas->find()
            ->contain(['Instituicoes'])
            ->where(['Visitas.id' => $id])
            ->firstOrFail();
        } catch (RecordNotFoundException $e) {
            $this->Flash->info(__('A visita que você tentou visualizar não existe.'));

            return $this->redirect(['action' => 'index', '?' => ['instituicao_id' => $id]]);
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
        $instituicao_id = $this->request->getQuery('instituicao_id');

        // When having this parameter them use it to filter the institutions
        if (!empty($instituicao_id)) {
            $this->set('instituicao_id', $instituicao_id);
        }
        $this->set('instituicoes', $this->Visitas->Instituicoes->find('list'));
        $this->set('visita', $visita);
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
        try {
            $visita = $this->Visitas->get($id, [
                'contain' => [],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->info(__('A visita que você tentou editar não existe.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Authorization->authorize($visita);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $visita = $this->Visitas->patchEntity($visita, $this->request->getData());
            if ($this->Visitas->save($visita)) {
                $this->Flash->success(__('The visita has been saved.'));

                return $this->redirect(['action' => 'view', $visita->id]);
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
        try {
            $visita = $this->Visitas->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->info(__('A visita que você tentou deletar não existe.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Authorization->authorize($visita);
        if ($this->Visitas->delete($visita)) {
            $this->Flash->success(__('The visita has been deleted.'));
        } else {
            $this->Flash->error(__('The visita could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
