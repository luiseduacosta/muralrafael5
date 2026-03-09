<?php

declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Event\EventInterface;
use App\Model\Table\AvaliacoesTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;
use function Cake\I18n\__;

/**
 * Avaliacoes Controller
 *
 * @property AvaliacoesTable $Avaliacoes
 * @method \App\Model\Entity\Avaliaco[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class AvaliacoesController extends AppController
{
    /**
     * paginate array
     */
    protected array $paginate = [
        'sortableFields' => ['id', 'timestamp']
    ];
    /**
     * Index method. Mostra os estágios de um aluno estagiario.
     *
     * @return Response|null|void Renders view
     */
    public function index($id = null)
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
            $avaliacoes = $this->paginate($query, ['sortableFields' => ['id', 'Estagiarios.Alunos.nome', 'Estagiarios.Instituicoes.instituicao', 'avaliacao1', 'timestamp']]);
        } elseif ($categoria == '2') {
            $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
            if ($estagiario_id) {
                $query = $this->fetchTable('Estagiarios')->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.id' => $estagiario_id]);
                if ($query->count() == 1) {
                    $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
                } else {
                    $this->Flash->error(__('Avaliações do Estagiário não encontradas'));
                    return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
                }
            } else {
                 // Fallback for students without estagiario_id in query
                 $query = $this->fetchTable('Estagiarios')->find()
                    ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes'])
                    ->where(['Estagiarios.aluno_id' => $user_data['aluno_id']]);
                 $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
            }
        } elseif ($categoria == '3') {
            $query = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Avaliacoes']);
            
            if ($user_data['professor_id']) {
                $query->where(['Estagiarios.professor_id' => $user_data['professor_id']]);
            } elseif ($user_data['supervisor_id']) {
                $query->where(['Estagiarios.supervisor_id' => $user_data['supervisor_id']]);
            }

            $estagiarios = $this->paginate($query, ['sortableFields' => ['id', 'Alunos.nome', 'periodo', 'nivel', 'Instituicoes.instituicao', 'Supervisores.nome', 'ch', 'nota']]);
        }

        // Initialize empty paginated results if still null to prevent template crashes
        if ($categoria == '1') {
            if (is_null($avaliacoes)) {
                $avaliacoes = $this->paginate($this->Avaliacoes);
            }
        } else {
            if (is_null($estagiarios)) {
                $estagiarios = $this->paginate($this->fetchTable('Estagiarios'));
            }
        }

        $this->set(compact('avaliacoes', 'estagiarios'));
    }
 
    /**
     * View method
     *
     * @param string|null $id Avaliaco id.
     * @param mixed $estagiario_id
     * @return Response|null|void Renders view
     * @throws RecordNotFoundException When record not found.
     */
    public function view($id = null)
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
            return $this->redirect(['controller' => 'Avaliacoes', 'action' => 'imprimeavaliacaopdf', '?' => ['estagiario_id' => $estagiario_id]]);
        }
    }

    /**
     * Add method
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        try {
            $this->Authorization->authorize($this->Avaliacoes);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        $avaliacaoexiste = null;
        
        if ($estagiario_id) {
            $avaliacaoexiste = $this->Avaliacoes->find()
                ->where(['estagiario_id' => $estagiario_id])
                ->first();
        } elseif ($id) {
            $avaliacaoexiste = $this->Avaliacoes->find()
                ->where(['id' => $id])
                ->first();
        }

        if ($avaliacaoexiste) {
            $this->Flash->error(__('Estagiário já foi avaliado'));
        }

        $avaliacao = $this->Avaliacoes->newEmptyEntity();
        if ($this->request->is('post')) {
            $avaliacaoresposta = $this->Avaliacoes->patchEntity($avaliacao, $this->request->getData());
            if ($this->Avaliacoes->save($avaliacaoresposta)) {
                $this->Flash->success(__('Avaliação registrada.'));

                return $this->redirect(['controller' => 'avaliacoes', 'action' => 'imprimeavaliacaopdf', $this->getRequest()->getData('estagiario_id')]);
            }
            $this->Flash->error(__('Avaliaçãoo no foi registrada. Tente novamente.'));
        }

        if ($estagiario_id) {
            $estagiario = $this->Avaliacoes->Estagiarios->find()
                ->contain(['Alunos'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        } else {
            $estagiario = $this->Avaliacoes->Estagiarios->find()
            ->contain(['Alunos'])
            ->first();
        }
        
        $this->set(compact('avaliacao', 'estagiario'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Avaliaco id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
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
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
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
     * @return Response|null|void Renders view
     */
    public function imprimeavaliacaopdf($id = null)
    {
        $this->Authorization->skipAuthorization();

        $estagiario_id = $this->request->getQuery('estagiario_id');

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }
        $this->layout = false;
        if ($id == null) {
            $this->Flash->info(__('Imprimir a folha de avaliação do estágio do aluno'));
            return $this->redirect(['controller' => 'Avaliacoes', 'action' => 'avaliacaomanualpdf', '?' => ['estagiario_id' => $estagiario_id]]);
        } else {
            $avaliacao = $this->Avaliacoes->find()
                ->contain(['Estagiarios' => ['Alunos', 'Supervisores', 'Professores', 'Instituicoes']])
                ->where(['Avaliacoes.id' => $id])
                ->first();
        }

        if (empty($avaliacao)) {
            $this->Flash->error(__('Sem avaliação on-line'));
            return $this->redirect(['controller' => 'Avaliacoes', 'action' => 'avaliacaomanualpdf', '?' => ['estagiario_id' => $estagiario_id]]);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true,
                'filename' => 'avaliacao_discente_' . $id . '.pdf'
            ]
        );
        $this->set('avaliacao', $avaliacao);
    }

    /**
     * Avaliacaomanualpdf method
     *
     * @return Response|null|void Renders view
     */
    public function avaliacaomanualpdf($id = null)
    {
        $this->Authorization->skipAuthorization();

        $this->layout = false;

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
                'filename' => 'avaliacao_discente_' . $id . '.pdf'
            ]
        );
        $this->set('estagiario', $estagiario);
    }
}