<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use DateTime;

/**
 * Inscricoes Controller
 *
 * @property \App\Model\Table\InscricoesTable $Inscricoes
 * @method \App\Model\Entity\Inscricao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InscricoesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();

        // Optimized periods query using distinct and list to save memory
        $periodos = $this->Inscricoes->find('list', [
            'keyField' => 'periodo',
            'valueField' => 'periodo',
        ])->distinct(['periodo'])->order(['periodo' => 'ASC'])->toArray();
        $periodos = ['all' => 'Todos'] + $periodos;

        $periodo = $this->request->getQuery('periodo') ?? $this->request->getData('periodo');
        if (empty($periodo)) {
            $periodo = end($periodos);
        }

        $query = $this->Inscricoes->find()
            ->select([
                'Inscricoes.id',
                'Inscricoes.aluno_id',
                'Inscricoes.registro',
                'Inscricoes.muralestagio_id',
                'Inscricoes.data',
                'Inscricoes.periodo',
                'Inscricoes.timestamp',
            ])
            ->contain([
                'Alunos' => [
                    'fields' => ['id', 'nome'],
                ],
                'Muralestagios' => [
                    'fields' => ['id', 'instituicao_id'],
                    'Instituicoes' => [
                        'fields' => ['id', 'instituicao'],
                    ],
                ],
            ]);

        if ($periodo !== 'all') {
            $query->where(['Inscricoes.periodo' => $periodo]);
        }

        if ($this->request->getQuery('aluno_id')) {
            $query->where(['Inscricoes.aluno_id' => $this->request->getQuery('aluno_id')]);
        }

        $inscricoes = $this->paginate($query, [
            'sortableFields' => [
                'id',
                'aluno_id',
                'registro',
                'muralestagio_id',
                'data',
                'periodo',
                'timestamp',
                'Alunos.nome',
                'Instituicoes.instituicao',
            ],
        ]);

        $this->set(compact('inscricoes', 'periodos', 'periodo'));
    }

    /**
     * View method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $inscricao = $this->Inscricoes->get($id, [
            'contain' => ['Alunos', 'Muralestagios' => ['Instituicoes']],
        ]);
        try {
            $this->Authorization->authorize($inscricao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Inscricoes', 'action' => 'index']);
        }
        $this->set(compact('inscricao'));
    }

    /**
     * Add method. O aluno e o admin podem fazer inscrição.
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add(?string $id = null)
    {
        $this->Authorization->skipAuthorization();
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $dados = $this->request->getData();

        $periodo = $this->configuracao->mural_periodo_atual;
        $dados['periodo'] = $periodo;

        $muralestagio_id = $this->getRequest()->getQuery('muralestagio_id');

        if (!$muralestagio_id) {
            $this->Flash->info(__('Selecione no mural de estagios'));

            return $this->redirectBack(['controller' => 'Muralestagios', 'action' => 'index']);
        } else {
            $muralestagio = $this->fetchTable('Muralestagios')->get($muralestagio_id);
            // $dados['muralestagio'] = $muralestagio;
            $dados['muralestagio_id'] = $muralestagio->id;

            /** Verifico o periodo do mural e comparo com o periodo da inscricao */
            if ($muralestagio->periodo != $periodo) {
                $this->Flash->error(__('O periodo de inscricao nao coincide com o periodo do Mural.'));

                return $this->redirectBack(['controller' => 'Muralestagios', 'action' => 'view', $muralestagio_id]);
            }
            $instituicao = $this->fetchTable('Instituicoes')->get($muralestagio->instituicao_id);
        }

        // Admin não pode fazer inscrição de aluno? Corrigir
        if ($user_data['aluno_id']) {
            $aluno = $this->fetchTable('Alunos')->get($user_data['aluno_id']);
            $dados['registro'] = $aluno->registro;
            $dados['aluno_id'] = $aluno->id;
        } else {
            $this->Flash->error(__('Selecione um aluno(a) para fazer a inscrição.'));

            return $this->redirectBack(['controller' => 'Alunos', 'action' => 'index']);
        }
        /** Verifico se já fez inscrição para não duplicar */
        $conditions = [
            'Inscricoes.aluno_id' => $aluno->id,
            'Inscricoes.muralestagio_id' => $muralestagio->id,
        ];
        $inscricao_duplicada = $this->Inscricoes->find()
            ->where($conditions)
            ->first();
        if ($inscricao_duplicada) {
            $this->Flash->error(__('Inscrição já realizada'));

            return $this->redirect(['controller' => 'Inscricoes', 'action' => 'view', $inscricao_duplicada->id]);
        }
        // Date atual
        $data = new DateTime();
        $dados['data'] = $data->format('Y-m-d');
        $inscricao = $this->Inscricoes->newEmptyEntity();
        $inscricao = $this->Inscricoes->patchEntity($inscricao, $dados);
        if ($this->Inscricoes->save($inscricao)) {
            $this->Flash->success(__('Inscricao realizada com sucesso.'));

            return $this->redirect(['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]);
        }
        $this->Flash->error(__('The inscricao could not be saved. Please, try again.'));
        $this->set(compact('inscricao', 'aluno', 'periodo', 'muralestagio', 'data', 'instituicao'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $inscricao = $this->Inscricoes->get($id, [
            'contain' => ['Alunos'],
        ]);

        try {
            $this->Authorization->authorize($inscricao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $inscricao = $this->Inscricoes->patchEntity($inscricao, $this->request->getData());
            if ($this->Inscricoes->save($inscricao)) {
                $this->Flash->success(__('The inscricao has been saved.'));

                return $this->redirect(['controller' => 'Inscricoes', 'action' => 'view', $inscricao->id]);
            }
            $this->Flash->error(__('The inscricao could not be saved. Please, try again.'));
        }
        $this->set(compact('inscricao'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Inscricao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inscricao = $this->Inscricoes->get($id);

        try {
            $this->Authorization->authorize($inscricao);
            if ($this->Inscricoes->delete($inscricao)) {
                $this->Flash->success(__('The inscricao has been deleted.'));

                return $this->redirect([
                    'controller' => 'Inscricoes',
                    'action' => 'index',
                    '?' => ['aluno_id' => $inscricao->aluno_id, 'periodo' => $inscricao->periodo],
                ]);
            } else {
                $this->Flash->error(__('The inscricao could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }
}
