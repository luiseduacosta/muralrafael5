<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;

/**
 * InstSuper Controller
 *
 * @property \App\Model\Table\InstSuperTable $InstSuper
 * @method \App\Model\Entity\InstSuper[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstSuperController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * Before filter method
     *
     * @param \Cake\Event\EventInterface $event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
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
        $this->Authorization->skipAuthorization();

        $instSuper = $this->paginate($this->InstSuper->find()->contain(['Instituicoes', 'Supervisores']));

        $this->set(compact('instSuper'));
    }

    /**
     * View method
     *
     * @param string|null $id InstSuper id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $this->Authorization->skipAuthorization();
        try {
            $instSuper = $this->InstSuper->get($id, [
                'contain' => ['Instituicoes', 'Supervisores'],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Registro instituicao-supervisor nao foi encontrado. Tente novamente.'));

            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('instSuper'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $instSuper = $this->InstSuper->newEmptyEntity();
        $this->Authorization->authorize($instSuper);
        if ($this->request->is('post')) {
            $instSuper = $this->InstSuper->patchEntity($instSuper, $this->request->getData());
            if ($this->InstSuper->save($instSuper)) {
                $this->Flash->success(__('Registro instituicao-supervisor inserido.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Registro instituicao-supervisor nao foi inserido. Tente novamente.'));
        }
        $instituicoes = $this->InstSuper->Instituicoes->find('list', [
            'keyField' => 'id',
            'valueField' => 'instituicao',
            'order' => ['Instituicoes.instituicao' => 'asc'],
        ]);
        $supervisores = $this->InstSuper->Supervisores->find('list', [
            'keyField' => 'id',
            'valueField' => 'nome',
            'order' => ['Supervisores.nome' => 'asc'],
        ]);
        $this->set(compact('instSuper', 'instituicoes', 'supervisores'));
    }

    /**
     * Edit method
     *
     * @param string|null $id InstSuper id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        try {
            $instSuper = $this->InstSuper->get($id, [
                'contain' => [],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Registro instituicao-supervisor nao foi encontrado. Tente novamente.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Authorization->authorize($instSuper);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $instSuper = $this->InstSuper->patchEntity($instSuper, $this->request->getData());
            if ($this->InstSuper->save($instSuper)) {
                $this->Flash->success(__('Registro instituicao-supervisor atualizado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Registro instituicao-supervisor nao foi atualizado. Tente novamente.'));
        }
        $instituicoes = $this->InstSuper->Instituicoes->find('list', [
            'keyField' => 'id',
            'valueField' => 'instituicao',
            'order' => ['Instituicoes.instituicao' => 'asc'],
        ]);
        $supervisores = $this->InstSuper->Supervisores->find('list', [
            'keyField' => 'id',
            'valueField' => 'nome',
            'order' => ['Supervisores.nome' => 'asc'],
        ]);
        $this->set(compact('instSuper', 'instituicoes', 'supervisores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id InstSuper id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $instSuper = $this->InstSuper->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Registro instituicao-supervisor nao foi encontrado. Tente novamente.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Authorization->authorize($instSuper);

        if ($this->InstSuper->delete($instSuper)) {
            $this->Flash->success(__('Registro instituicao-supervisor excluido.'));
        } else {
            $this->Flash->error(__('Registro instituicao-supervisor nao foi excluido. Tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
