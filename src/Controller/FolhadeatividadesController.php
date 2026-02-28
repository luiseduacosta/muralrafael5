<?php

declare(strict_types=1);

namespace App\Controller;
use App\Model\Entity\Folhadeatividade;

/**
 * Folhadeatividades Controller
 *
 * @property \App\Model\Table\FolhadeatividadesTable $Folhadeatividades
 * @method \App\Model\Entity\Folhadeatividade[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FolhadeatividadesController extends AppController
{

    /**
     * Index method
     * @param string|null $id Estagiario.id
     * $id = estagiario_id
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($id = null)
    {
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        $estagiariotabela = $this->fetchTable('Estagiarios');
        if ($estagiario_id) {
            $folhadeatividades = $this->Folhadeatividades->find('all')
                ->order(['id'])
                ->where(['estagiario_id' => $estagiario_id]);

            $estagiario = $estagiariotabela->find()
                ->contain(['Alunos', 'Instituicoes'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        }

        
        if (empty($estagiario)) { 
            $this->Flash->error(__('Selecione o estagiário'));
            $estagiario = $estagiariotabela->find()
                ->contain(['Alunos', 'Instituicoes'])
                ->first();
            $folhadeatividades = $this->Folhadeatividades->find('all')
                ->order(['id'])
                ->where(['estagiario_id' => $estagiario->id]);
        }

        if (empty($folhadeatividades)) {
            $this->Flash->error(__('Selecione o estagiário e o período da folha de atividades'));
            //return $this->redirect(['controller' => 'estagiarios', 'action' => 'index']);
        }

        $folhadeatividades = $this->paginate($folhadeatividades);

        $this->set(compact('estagiario', 'folhadeatividades'));
    }

    /**
     * Add method
     * @param string|null $id Estagiario.id
     * $id = estagiario_id
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function add($id = NULL)
    {
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

        if ($estagiario_id) {
            $folhadeatividades = $this->Folhadeatividades->find('all')
                ->order(['Folhadeatividades.id'])
                ->contain(['Estagiarios' => ['Alunos', 'Instituicoes']])
                ->where(['estagiario_id' => $estagiario_id]);

            $estagiariotabela = $this->fetchTable('Estagiarios');
            $estagiario = $estagiariotabela->find()
                ->contain(['Alunos', 'Instituicoes'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        }

        if (empty($folhadeatividades)) {
            $this->Flash->error(__('Selecione o estagiário e o período da folha de atividades'));
            //return $this->redirect(['controller' => 'folhadeatividades', 'action' => 'add', '?' => ['estagiario_id' => $estagiario_id]]);
        } else {
            $this->set('folhadeatividades', $folhadeatividades);
        }

        $folhadeatividadeentity = $this->Folhadeatividades->newEmptyEntity();

        if ($this->request->is('post')) {
            // pr($this->request->getData());
            $dados = $this->request->getData();
            $dados['horario'] = null;
            // pr($dados);
            // die();
            $folhadeatividaderesposta = $this->Folhadeatividades->patchEntity($folhadeatividadeentity, $dados);
            // pr($folhadeatividaderesposta);
            // die();
            if ($this->Folhadeatividades->save($folhadeatividaderesposta)) {
                $this->Flash->success(__('Atividades cadastrada!'));

                return $this->redirect(['action' => 'view', $folhadeatividaderesposta->id]);
            }
            $this->Flash->error(__('Atividade não foi cadastrada. Tente mais uma vez.'));
        } else {
            // die('post');
        }

        
        if (!empty($folhadeatividades)) { $this->set('estagiario', $estagiario); }
    }

    /**
     * View method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        if ($estagiario_id) {
            $folhadeatividade = $this->Folhadeatividades->find()
                ->where(['estagiario_id' => $estagiario_id])
                ->first();
        } else {
            $folhadeatividade = $this->Folhadeatividades->get($id, [
                'contain' => ['Estagiarios'],
            ]);
        }

        if (!isset($folhadeatividade)) {
            $this->Flash->error(__('Sem atividades cadastradas'));
            return $this->redirect(['controller' => 'estagiarios', 'action' => 'view', isset($estagiario_id) ? $estagiario_id : $id]);
        }
        $this->set(compact('folhadeatividade'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $folhadeatividade = $this->Folhadeatividades->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $folhadeatividade = $this->Folhadeatividades->patchEntity($folhadeatividade, $this->request->getData());
            if ($this->Folhadeatividades->save($folhadeatividade)) {
                $this->Flash->success(__('Atividade atualizada.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('Não foi possível atualizar. Tente outra vez.'));
        }
        $estagiarioquery = $this->Folhadeatividades->find()
            ->where(['Folhadeatividades.id' => $id])
            ->contain(['Estagiarios' => ['Alunos']])
            ->select(['Estagiarios.id', 'Alunos.nome']);
        // pr($estagiarioquery->first());
        $estagiario = $estagiarioquery->first();
        // pr($estagiario);
        // die();
        $this->set(compact('folhadeatividade', 'estagiario'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $folhadeatividade = $this->Folhadeatividades->get($id);
        $estagiariotabela = $this->fetchTable('Estagiarios');
        $estagiario = $estagiariotabela->find()
            ->where(['id' => $folhadeatividade->estagiario_id])
            ->first();

        if ($this->Folhadeatividades->delete($folhadeatividade)) {
            $this->Flash->success(__('Registro de atividade excluido.'));
            return $this->redirect(['controller' => 'estagiarios', 'action' => 'view', $estagiario->id]);
        } else {
            $this->Flash->error(__('Registro de atividade nao foi excluido. Tente novamente.'));
            return $this->redirect(['controller' => 'folhadeatividades', 'action' => 'view', $id]);
        }
    }


    /**
     * Imprimefolhadeatividades method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function imprimefolhadeatividades($id = null)
    {

        $registro = $this->getRequest()->getQuery('registro');
        if ($registro) {
            $estagiariotable = $this->fetchTable('Estagiarios');
            $estagiario = $estagiariotable->find()
                ->where(['registro' => $registro])
                ->first();
            if ($estagiario) {

                $this->viewBuilder()->enableAutoLayout(false);
                $this->viewBuilder()->setClassName('CakePdf.Pdf');
                $this->viewBuilder()->setOption(
                    'pdfConfig',
                    [
                        'orientation' => 'portrait',
                        'download' => true, // This can be omitted if "filename" is specified.
                        'filename' => 'atividades_de_estagio_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
                    ]
                );
                $this->set('estagiario', $estagiario);
            } else {
                $this->Flash->error(__('Aluno ainda sem estágio. Tente novamente'));
                return $this->redirect(['controller' => 'alunos', 'action' => 'view', '?' => ['registro' => $registro]]);
            }
        } else {
            $this->Flash->error(__('Sem número de registro. Tente novamente'));
            return $this->redirect(['controller' => 'alunos', 'action' => 'index']);
        }
    }

    /**
     * Exadd method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function exadd($id = NULL)
    {

        /** Verifica se há estagiários */
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        $estagiariostabela = $this->fetchTable('Estagiarios');
        $estagiario = $estagiariostabela->find()
            ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Professores'])
            ->where(['Estagiarios.id' => $estagiario_id])
            ->first();

        if (!$estagiario) {
            $this->Flash->error(__('Aluno sem estágio cadastrado'));
            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'view', $estagiario_id]);
        }

        $atividadesrealizadas = $this->Folhadeatividades->find()
            ->contain(['Estagiarios' => ['Alunos', 'Supervisores', 'Professores', 'Instituicoes']])
            ->where(['estagiario_id' => $estagiario_id])
            ->limit(1)
            ->first();

        $folhadeatividade = $this->Folhadeatividades->newEmptyEntity();

        if ($this->request->is('post')) {
            $folhadeatividaderesposta = $this->Folhadeatividades->patchEntity($folhadeatividade, $this->request->getData());
            if ($this->Folhadeatividades->save($folhadeatividaderesposta)) {
                $this->Flash->success(__('Atividades cadastrada!'));

                return $this->redirect(['action' => 'view', $folhadeatividaderesposta->id]);
            }
            $this->Flash->error(__('Atividade não foi cadastrada. Tente mais uma vez.'));
        }

        $this->set(compact('folhadeatividade', 'atividadesrealizadas'));
    }
    
    public function selecionafolhadeatividades($id = NULL)
    {

        /* No login foi capturado o id do estagiário */
        $id = $this->getRequest()->getSession()->read('estagiario_id');
        //$this->layout = false;
        if (is_null($id)) {
            $this->Flash->error(__('Selecione o estagiário e o período da folha de atividades'));

            $estagiarios = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes']);
            //return $this->redirect('/estagiarios/index');
            
        } else {
            $estagiarios = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.registro' => $this->getRequest()->getSession()->read('registro')])
                ->orderByDesc('nivel');
            //return $this->redirect(['controller' => 'alunos', 'action' => 'view', $estagiario->aluno_id]);
        }
        $this->set('estagiarios', $this->paginate($estagiarios));
        // pr($estagiarios);
        // die();
    }

    public function folhadeatividadespdf($id = NULL)
    {

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        // pr($estagiario_id);
        $this->layout = false;
        $atividades = $this->Folhadeatividades->find()
            ->contain(['Estagiarios' => ['Alunos', 'Professores', 'Instituicoes', 'Supervisores']])
            ->where(['Folhadeatividades.estagiario_id' => $estagiario_id])
            ->all();
        // debug($atividades);
// pr($atividades);

        $estagiario = $this->Folhadeatividades->Estagiarios->find()
            ->contain(['Alunos', 'Professores', 'Instituicoes', 'Supervisores'])
            ->where(['Estagiarios.id' => $estagiario_id])
            ->first();
        // debug($estagiario);
// pr($estagiario);
// die();
        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true, // This can be omitted if "filename" is specified.
                'filename' => 'folha_de_atividades_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
            ]
        );
        $this->set('atividades', $atividades);
        $this->set('estagiario', $estagiario);
    }
}
