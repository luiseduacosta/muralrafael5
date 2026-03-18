<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Turmas Controller
 *
 * @property \App\Model\Table\TurmasTable $Turmas
 * @method \App\Model\Entity\Turma[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TurmasController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Turmas);
        $turmas = $this->paginate($this->Turmas);

        $this->set(compact('turmas'));
    }

    /**
     * View method
     *
     * @param string|null $id Turma id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        //ini_set('memory_limit', '2048M');
        $turma = $this->Turmas->get($id, [
            'contain' => [
               /* 'Estagiarios' => ['Alunos', 'Instituicoes', 'Supervisores', 'Professores'],*/
               'Muralestagios' => ['Instituicoes', 'Professores'],
            ],
        ]);
        $this->Authorization->authorize($turma);

        $this->set(compact('turma'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->authorize($this->Turmas);
        $turma = $this->Turmas->newEmptyEntity();
        if ($this->request->is('post')) {
            $turma = $this->Turmas->patchEntity($turma, $this->request->getData());
            if ($this->Turmas->save($turma)) {
                $this->Flash->success(__('The turma has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The turma could not be saved. Please, try again.'));
        }
        $this->set(compact('turma'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Turma id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $turma = $this->Turmas->get($id, [
            'contain' => [],
        ]);
        $this->Authorization->authorize($turma);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $turma = $this->Turmas->patchEntity($turma, $this->request->getData());
            if ($this->Turmas->save($turma)) {
                $this->Flash->success(__('The turma has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The turma could not be saved. Please, try again.'));
        }
        $this->set(compact('turma'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Turma id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $turma = $this->Turmas->get($id);
        $this->Authorization->authorize($turma);
        if ($this->Turmas->delete($turma)) {
            $this->Flash->success(__('The turma has been deleted.'));
        } else {
            $this->Flash->error(__('The turma could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
