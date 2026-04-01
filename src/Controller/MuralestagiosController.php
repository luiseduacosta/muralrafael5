<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;

/**
 * Muralestagios Controller
 *
 * @property \App\Model\Table\MuralestagiosTable $Muralestagios
 * @method \App\Model\Entity\Muralestagio[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MuralestagiosController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->authorize($this->Muralestagios);
        $pass = $this->getRequest()->getParam('pass');
        $periodo = $pass ? $pass[0] : $this->configuracao->mural_periodo_atual;
        $this->set('periodo', $periodo);

        $contained = ['Instituicoes'];

        if ($periodo == 'all') {
            $muralestagios = $this->Muralestagios->find('all')
            ->contain($contained);
        } else {
            $muralestagios = $this->Muralestagios->find('all', ['conditions' => ['Muralestagios.periodo' => $periodo]])
            ->contain($contained);
        }

        $this->set('muralestagios', $this->paginate($muralestagios, [
            'sortableFields' => [
                'id',
                'instituicao',
                'vagas',
                'beneficios',
                'final_de_semana',
                'carga_horaria',
                'data_inscricao',
                'data_selecao',
            ],
            'order' => ['data_inscricao' => 'desc'],
        ]));

        $periodototal = $this->Muralestagios->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo',
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, ['all' => 'Todos']);
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
    public function view(?string $id = null)
    {
        $user_session = $this->request->getAttribute('identity');

        $muralestagio = $this->Muralestagios->get($id, [
            'contain' => ['Instituicoes', 'Inscricoes' => ['Alunos']],
        ]);

        if (empty($user_session)) {
            $this->Flash->error('Authorization error: User not authenticated.');

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }
        $this->set(compact('muralestagio'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        try {
            $this->Authorization->authorize($this->Muralestagios);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $periodo = $this->configuracao->mural_periodo_atual;

        $muralestagio = $this->Muralestagios->newEmptyEntity();
        if ($this->request->is('post')) {
            if ($this->request->getData('convenio') == '0') {
                $this->Flash->error(__('Instituição não conveniada.'));

                return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
            }

            // Get Instituicao name
            $instituicao = $this->fetchTable('Instituicoes')
            ->find()
            ->select(['id', 'instituicao'])
            ->where(['Instituicoes.id' => $this->request->getData('instituicao_id')])
            ->first();

            // Put the instituicao name in the data
            $dados = $this->request->getData();
            $dados['instituicao'] = $instituicao->instituicao;
            $dados['periodo'] = $periodo;

            // Horario de seleção has only 5 digits
            $dados['horario_selecao'] = substr($dados['horario_selecao'], 0, 5);

            $muralestagio = $this->Muralestagios->patchEntity($muralestagio, $dados);

            if ($this->Muralestagios->save($muralestagio)) {
                $this->Flash->success(__('Registro de mural de estágio feito com sucesso.'));

                return $this->redirect(['action' => 'view', $muralestagio->id]);
            }
            $this->Flash->error(__('Registro de mural de estágio não foi feito. Tente novamente.'));
        }
        $instituicoes = $this->fetchTable('Instituicoes')->find('list');
        $this->set(compact('muralestagio', 'instituicoes', 'periodo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $muralestagio = $this->Muralestagios->get($id);

        try {
            $this->Authorization->authorize($muralestagio);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Get Instituicao name
            $instituicao = $this->fetchTable('Instituicoes')
            ->find()
            ->select(['id', 'instituicao'])
            ->where(['Instituicoes.id' => $this->request->getData('instituicao_id')])
            ->first();

            // Put the instituicao name in the data
            $dados = $this->request->getData();
            $dados['instituicao'] = $instituicao->instituicao;

            // Horario de seleção has only 5 digits
            $dados['horario_selecao'] = substr($dados['horario_selecao'], 0, 5);

            $muralestagio = $this->Muralestagios->patchEntity($muralestagio, $dados);

            if ($this->Muralestagios->save($muralestagio)) {
                $this->Flash->success(__('O mural de estágio foi salvo com sucesso.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('O mural de estágio não foi salvo. Tente novamente.'));
        }
        $instituicoes = $this->fetchTable('Instituicoes')->find('list');
        $this->set(compact('muralestagio', 'instituicoes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $muralestagio = $this->Muralestagios->get($id, ['contain' => 'Inscricoes']);

        try {
            $this->Authorization->authorize($muralestagio);

            // If have inscricoes not delete
            if (count($muralestagio->inscricoes) > 0) {
                $this->Flash->warning(__('Inscrições associadas a este Mural de estágios'));

                return $this->redirect(['controller' => 'Muralestagios', 'action' => 'view', $id]);
            }

            if ($this->Muralestagios->delete($muralestagio)) {
                $this->Flash->success(__('O mural de estágio foi excluído com sucesso.'));
            } else {
                $this->Flash->error(__('O mural de estágio não foi excluído. Tente novamente.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Imprimir PDF com as inscrições para seleção do estagiário
     *
     * @param string|null $id Muralestagio id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function imprimepdf(?string $id = null)
    {
        $muralestagio = $this->Muralestagios->find()
            ->contain(['Inscricoes' => [
                'sort' => ['Alunos.nome' => 'ASC'],
                'Alunos',
            ]])
            ->where(['Muralestagios.id' => $id])
            ->first();

        try {
            $this->Authorization->authorize($muralestagio);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
        ]);

        $this->set(compact('muralestagio'));
    }
}
