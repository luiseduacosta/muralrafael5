<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;

/**
 * Avaliacoes Controller
 *
 * @property \App\Controller\AvaliacoesTable $Avaliacoes
 * @method \App\Model\Entity\Avaliaco[]|\App\Controller\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AvaliacoesController extends AppController
{
    /**
     * paginate array
     */
    protected array $paginate = [
        'sortableFields' => ['id', 'timestamp'],
    ];

    /**
     * Index method. Mostra os estágios de um aluno estagiario.
     *
     * @param string|null $id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index(?string $id = null)
    {
        try {
            $this->Authorization->authorize($this->Avaliacoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $avaliacoes = null;
        $estagiarios = null;
        $categoria = $user_data['categoria'] ?? null;

        if ($categoria == '1') {
            $query = $this->Avaliacoes->find()->contain(['Estagiarios' => ['Alunos', 'Instituicoes']]);
            $avaliacoes = $this->paginate($query, [
                'sortableFields' => [
                    'id',
                    'Estagiarios.Alunos.nome',
                    'Estagiarios.Instituicoes.instituicao',
                    'avaliacao1',
                    'timestamp',
                ],
            ]);
        } elseif ($categoria == '2') {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            if ($estagiario_id) {
                $query = $this->fetchTable('Estagiarios')->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.id' => $estagiario_id, 'Estagiarios.aluno_id' => $user_data['aluno_id']]);
                if ($query->count() == 1) {
                    $estagiarios = $this->paginate($query, [
                        'sortableFields' => [
                            'id',
                            'Alunos.nome',
                            'periodo',
                            'nivel',
                            'Instituicoes.instituicao',
                            'Supervisores.nome',
                            'ch',
                            'nota',
                        ],
                    ]);
                } else {
                    $this->Flash->error(__('Avaliações do Estagiário não encontradas'));

                    return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
                }
            } else {
                 $query = $this->fetchTable('Estagiarios')->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.aluno_id' => $user_data['aluno_id']]);
                 $estagiarios = $this->paginate($query, [
                     'sortableFields' => [
                         'id',
                         'Alunos.nome',
                         'periodo',
                         'nivel',
                         'Instituicoes.instituicao',
                         'Supervisores.nome',
                         'ch',
                         'nota',
                     ],
                 ]);
            }
        } elseif ($categoria == '3') {
            $query = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                ->where(['Estagiarios.professor_id' => $user_data['professor_id']]);

            $estagiarios = $this->paginate($query, [
                'sortableFields' => [
                    'id',
                    'Alunos.nome',
                    'periodo',
                    'nivel',
                    'Instituicoes.instituicao',
                    'Supervisores.nome',
                    'ch',
                    'nota',
                ],
            ]);
        } elseif ($categoria == '4') {
            $query = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                ->where(['Estagiarios.supervisor_id' => $user_data['supervisor_id']]);

            $estagiarios = $this->paginate($query, [
                'sortableFields' => [
                    'id',
                    'Alunos.nome',
                    'periodo',
                    'nivel',
                    'Instituicoes.instituicao',
                    'Supervisores.nome',
                    'ch',
                    'nota',
                ],
            ]);
        }

        $this->set(compact('avaliacoes', 'estagiarios'));
    }

    /**
     * View method
     *
     * @param string|null $id Avaliaco id.
     * @param mixed $estagiario_id
     * @return \App\Controller\Response|null|void Renders view
     * @throws \App\Controller\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $contained = ['Estagiarios' => ['Alunos', 'Instituicoes', 'Professores', 'Supervisores']];

        if ($id) {
            $avaliacao = $this->Avaliacoes->find()->contain($contained)
                ->where(['Avaliacoes.id' => $id])
                ->first();
        } else {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            $avaliacao = $this->Avaliacoes->find()->contain($contained)
                ->where(['Avaliacoes.estagiario_id' => $estagiario_id])
                ->first();
        }

        if ($avaliacao) {
            try {
                $this->Authorization->authorize($avaliacao);
            } catch (ForbiddenException $error) {
                $this->Flash->error('Authorization error: ' . $error->getMessage());

                return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
            }
            $this->set(compact('avaliacao'));
        } else {
            $this->Flash->error(__('Aluno sem avaliaçao online'));
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

            return $this->redirect([
                'controller' => 'Avaliacoes',
                'action' => 'imprimeavaliacaopdf',
                '?' => ['estagiario_id' => $estagiario_id],
            ]);
        }
    }

    /**
     * Add method
     *
     * @param string|null $id
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add(?string $id = null)
    {
        try {
            $this->Authorization->authorize($this->Avaliacoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

        if (empty($estagiario_id)) {
            $this->Flash->error(__('Parâmetro estagiário é obrigatório.'));

            return $this->redirect(['action' => 'index']);
        }

        $avaliacaoexiste = $this->Avaliacoes->find()
            ->where(['estagiario_id' => $estagiario_id])
            ->first();

        if ($avaliacaoexiste) {
            $this->Flash->error(__('O(A) estagiário(a) já possui avaliação.'));

            return $this->redirect(['action' => 'view', $avaliacaoexiste->id]);
        }

        $avaliacao = $this->Avaliacoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $avaliacao = $this->Avaliacoes->patchEntity($avaliacao, $this->request->getData());
            if ($this->Avaliacoes->save($avaliacao)) {
                $this->Flash->success(__('Avaliação registrada com sucesso.'));

                return $this->redirect(['action' => 'view', $avaliacao->id]);
            }
            $this->Flash->error(__('Avaliação não foi registrada. Tente novamente.'));
        }

        $estagiario = $this->fetchTable('Estagiarios')->get($estagiario_id, [
            'contain' => ['Alunos'],
        ]);

        $this->set(compact('avaliacao', 'estagiario'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Avaliaco id.
     * @return \App\Controller\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \App\Controller\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $avaliacao = $this->Avaliacoes->get($id, [
            'contain' => ['Estagiarios' => 'Alunos'],
        ]);

        try {
            $this->Authorization->authorize($avaliacao);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $estagiario = $avaliacao->estagiario;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $avaliacao = $this->Avaliacoes->patchEntity($avaliacao, $this->request->getData());
            if ($this->Avaliacoes->save($avaliacao)) {
                $this->Flash->success(__('Avaliacao atualizada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Avaliaçao não foi atualizada. Tente novamente.'));

            return $this->redirect(['action' => 'edit', $id]);
        }

        $this->set(compact('avaliacao', 'estagiario'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Avaliaco id.
     * @return \App\Controller\Response|null|void Redirects to index.
     * @throws \App\Controller\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $avaliacao = $this->Avaliacoes->get($id);

        try {
            $this->Authorization->authorize($avaliacao);
            if ($this->Avaliacoes->delete($avaliacao)) {
                $this->Flash->success(__('Avaliacao excluida.'));
            } else {
                $this->Flash->error(__('Avaliacao nao foi excluida. Tente novamente.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Imprimeavaliacaopdf method
     *
     * @param string|null $id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function imprimeavaliacaopdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        if (empty($id)) {
            $this->Flash->error(__('Parâmetro estagiário é obrigatório.'));

            return $this->redirectBack(['action' => 'index']);
        }

        $avaliacao = $this->Avaliacoes->find()
            ->contain(['Estagiarios' => ['Alunos', 'Supervisores', 'Professores', 'Instituicoes']])
            ->where(['Avaliacoes.id' => $id])
            ->first();

        if (empty($avaliacao)) {
            $this->Flash->error(__('Sem avaliação on-line'));

            return $this->redirect([
                'controller' => 'Avaliacoes',
                'action' => 'avaliacaomanualpdf',
                '?' => ['estagiario_id' => $id],
            ]);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true,
                'filename' => 'avaliacao_discente_' . $avaliacao->id . '.pdf',
            ],
        );
        $this->set('avaliacao', $avaliacao);
    }

    /**
     * Avaliacaomanualpdf method
     *
     * @param string|null $id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function avaliacaomanualpdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        $this->viewBuilder()->setLayout(null);

        $estagiario_id = $this->request->getQuery('estagiario_id');

        if ($estagiario_id) {
            $estagiario = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Professores', 'Supervisores', 'Instituicoes'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        } else {
            $this->Flash->error(__('Sem parâmetros para localizar o(a) estagiário(a)'));

            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'index']);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true,
                'filename' => 'avaliacao_discente_' . $id . '.pdf',
            ],
        );
        $this->set('estagiario', $estagiario);
    }
}
