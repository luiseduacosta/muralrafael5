<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Instituicoes Controller
 *
 * @property \App\Model\Table\InstituicoesTable $Instituicoes
 * @method \App\Model\Entity\Instituicao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstituicoesController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        try {
            $this->Authorization->authorize($this->Instituicoes);
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
        //ini_set('memory_limit', '2048M');
        $instituicao = $this->Instituicoes->get($id, [
            'contain' => [
                'Areas',
                'Supervisores'=> ['Users'],
                'Estagiarios' => ['Alunos', 'Professores', 'Supervisores', 'Instituicoes', 'Turmas'],
                'Muralestagios' => ['Instituicoes', 'Professores'],
                'Visitas' 
            ],
        ]);

        $this->set(compact('instituicao'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $instituicao = $this->Instituicoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->Instituicoes->Areas->find('list');
        $supervisores = $this->Instituicoes->Supervisores->find('list');
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
        $instituicao = $this->Instituicoes->get($id, [
            'contain' => ['Supervisores'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instituicao = $this->Instituicoes->patchEntity($instituicao, $this->request->getData());
            if ($this->Instituicoes->save($instituicao)) {
                $this->Flash->success(__('The instituicao has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The instituicao could not be saved. Please, try again.'));
        }
        $areas = $this->Instituicoes->Areas->find('list');
        $supervisores = $this->Instituicoes->Supervisores->find('list');
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
        $instituicao = $this->Instituicoes->get($id);
        if ($this->Instituicoes->delete($instituicao)) {
            $this->Flash->success(__('The instituicao has been deleted.'));
        } else {
            $this->Flash->error(__('The instituicao could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
