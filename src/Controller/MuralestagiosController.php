<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Muralestagios Controller
 *
 * @property \App\Model\Table\MuralestagiosTable $Muralestagios
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MuralestagiosController extends AppController {
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($id = null)
    {
        $periodo = $this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        $this->set('periodo', $periodo);
        
        $contained = ['Instituicoes', 'Professores'];
        
        if ($periodo == 'all') {
            $muralestagios = $this->Muralestagios->find('all')
            ->contain($contained);
        } else {
            $muralestagios = $this->Muralestagios->find('all', ['conditions' => ['Muralestagios.periodo' => $periodo] ])
            ->contain($contained);
        }
        
        $this->set('muralestagios', $this->paginate($muralestagios));

        $periodototal = $this->Muralestagios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo'
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, array('all' => 'Todos'));
        $periodos = array_reverse($periodos);
        
        $this->set('periodos', $periodos);
    }

    /**
     * View method
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $muralestagio = $this->Muralestagios->get($id, [
            'contain' => ['Instituicoes', 'Turmas', 'Turnos', 'Professores', 'Inscricoes' => ['Alunos']],
        ]);
        
        $this->set(compact('muralestagio'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $periodo = $this->fetchTable("Configuracoes")->find()->first()['mural_periodo_atual'];
        
        $muralestagio = $this->Muralestagios->newEmptyEntity();
        if ($this->request->is('post')) {
            $muralestagio = $this->Muralestagios->patchEntity($muralestagio, $this->request->getData());
            if ($this->Muralestagios->save($muralestagio)) {
                $this->Flash->success(__('Registro de mural de estágio feito com sucesso.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Registro de mural de estágio não foi feito. Tente novamente.'));
        }
        $professores = $this->Muralestagios->Professores->find('list');
        $instituicoes = $this->Muralestagios->Instituicoes->find('list');
        $turmas = $this->Muralestagios->Turmas->find('list');
        $turnos = $this->Muralestagios->Turnos->find('list');
        $this->set(compact('muralestagio', 'instituicoes', 'turmas', 'turnos', 'professores', 'periodo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $muralestagio = $this->Muralestagios->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $muralestagio = $this->Muralestagios->patchEntity($muralestagio, $this->request->getData());
            if ($this->Muralestagios->save($muralestagio)) {
                $this->Flash->success(__('The muralestagio has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The muralestagio could not be saved. Please, try again.'));
        }
        $instituicoes = $this->Muralestagios->Instituicoes->find('list');
        $turmas = $this->Muralestagios->Turmas->find('list');
        $turnos = $this->Muralestagios->Turnos->find('list');
        $professores = $this->Muralestagios->Professores->find('list', ['limit' => 500]);
        $this->set(compact('muralestagio', 'instituicoes', 'turmas', 'turnos', 'professores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $muralestagio = $this->Muralestagios->get($id);
        if ($this->Muralestagios->delete($muralestagio)) {
            $this->Flash->success(__('The muralestagio has been deleted.'));
        } else {
            $this->Flash->error(__('The muralestagio could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
