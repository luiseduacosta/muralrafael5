<?php

declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;

/**
 * Instituicoes Controller
 *
 * @property \App\Model\Table\InstituicoesTable $Instituicoes
 * @method \App\Model\Entity\Instituicao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstituicoesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Instituicoes);
        $instituicoes = $this->paginate($this->Instituicoes->find()->contain(['Areas']));

        $this->set(compact('instituicoes'));
    }

    /**
     * View method
     *
     * @param string|null $id Instituicaoestagio id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $instituicao = $this->Instituicoes->find()->contain(['Areas', 'Supervisores'=> ['Users'], 'Estagiarios' => ['Alunos', 'Professores', 'Supervisores', 'Instituicoes', 'Turmas'], 'Muralestagios' => ['Instituicoes', 'Professores'], 'Visitas'])->where(['Instituicoes.id' => $id])->first();
        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $this->set(compact('instituicao'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->Authorization->authorize($this->Instituicoes);
        $instituicao = $this->Instituicoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->fetchTable('Areas')->find()->select(['id', 'nome'])->toArray();
        $supervisores = $this->fetchTable('Supervisores')->find()->select(['id', 'nome'])->toArray();
        $this->set(compact('instituicao', 'areas', 'supervisores'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Instituicao id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $instituicao = $this->Instituicoes->find()->contain(['Supervisores'])->where(['Instituicoes.id' => $id])->first();
        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['controller' => 'Instituicoes', 'action' => 'view', $id]);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->fetchTable('Areas')->find()->select(['id', 'nome'])->toArray();
        $supervisores = $this->fetchTable('Supervisores')->find()->select(['id', 'nome'])->toArray();
        $this->set(compact('instituicao', 'areas', 'supervisores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Instituicao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instituicao = $this->Instituicoes->get($id, ['contain' => ['Estagiarios']]);
        try {
            $this->Authorization->authorize($instituicao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'index']);
        }
        // If the instituicao has estagiarios, show an error message and return to the view
        if (sizeof($instituicao->estagiarios) > 0) {
            $this->Flash->error(__('Erro ao deletar: A instituicao tem estagiários associados.'));
            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'view', $id]);
        }

        try {
            if ($this->Instituicoes->delete($instituicao)) {
                $this->Flash->success(__('The instituicao has been deleted.'));
            } else {
                $this->Flash->error(__('The instituicao could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['controller' => 'Instituicoes', 'action' => 'index']);
        }
    }
}
