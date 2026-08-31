<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Http\Response;
use Cake\ORM\Query;
use function in_array;

/**
 * Professores Controller
 *
 * @property \App\Model\Table\ProfessoresTable $Professores
 * @method \App\Model\Entity\Professor[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfessoresController extends AppController
{
    private const STATUS_LABELS = [
        'ativo' => 'Ativo',
        'aposentado' => 'Aposentado',
        'inativo' => 'Inativo',
    ];

    private const STATUS_ALIASES = [
        'ativo' => ['ativo', 'active', 'activo'],
        'aposentado' => ['aposentado', 'retired'],
        'inativo' => ['inativo', 'inactive', 'inactivo'],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $this->Authorization->authorize($this->Professores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $query = $this->Professores->find()->contain(['Users']);

        $busca = $this->request->getQuery('busca');
        if ($busca) {
            $query->where([
                'OR' => [
                    'Professores.nome LIKE' => '%' . $busca . '%',
                    'Professores.cpf LIKE' => '%' . $busca . '%',
                    'Professores.siape LIKE' => '%' . $busca . '%',
                    'Professores.email LIKE' => '%' . $busca . '%',
                    'Professores.celular LIKE' => '%' . $busca . '%',
                ],
            ]);
        }

        $statusFilter = $this->request->getQuery('status');
        if ($statusFilter) {
            $canonical = $this->canonicalStatus((string)$statusFilter);
            $aliases = self::STATUS_ALIASES[$canonical] ?? [$canonical];
            $query->where(['Professores.status IN' => $aliases]);
        }

        $professores = $this->paginate($query, [
            'order' => ['Professores.nome' => 'ASC'],
            'sortableFields' => [
                'id',
                'Professores.nome',
                'cpf',
                'siape',
                'email',
                'celular',
                'curriculolattes',
                'departamento',
                'tipocargo',
                'status',
                'motivoegresso',
                'estagiarios_count',
            ],
        ]);

        $statusList = self::STATUS_LABELS;

        $this->set(compact('professores', 'statusFilter', 'statusList'));
    }

    /**
     * View method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $professor = $this->Professores->get($id, [
            'contain' => ['Users', 'Estagiarios' => ['Instituicoes']],
        ]);

        try {
            $this->Authorization->authorize($professor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        $this->paginate = [
            'Estagiarios' => ['limit' => 5, 'scope' => 'estagiario'],
        ];

        $estagiarios = $this->paginate($this->Professores->Estagiarios->find('all', [
            'contain' => ['Alunos' => ['Turnos'], 'Instituicoes', 'Supervisores'],
        ])->innerJoinWith('Professores', function (Query $query) use ($professor) {
            return $query->where([
                'professor_id' => $professor->id,
            ]);
        }));

        $this->set(compact('professor', 'estagiarios'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        try {
            $this->Authorization->authorize($this->Professores);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        if ($user_data['professor_id'] > 0) {
            $this->Flash->warning(__('Professor já está cadastrado.'));

            return $this->redirect(['action' => 'view', $user_data['professor_id']]);
        }

        $professor = $this->Professores->newEmptyEntity();
        $professor->status = 'ativo';

        if ($this->request->is('post')) {
            $professor = $this->Professores->patchEntity($professor, $this->request->getData());

            if ($user_data['professor_id']) {
                $user = $this->Authentication->getIdentity();
                $professor->user_id = $user->get('id');
            }

            if ($this->Professores->save($professor)) {
                $this->Flash->success(__('The professor has been saved.'));

                return $this->redirect(['action' => 'view', $professor->id]);
            }
            $this->Flash->error(__('The professor could not be saved. Please, try again.'));
        }

        if ($user_data['professor_id']) {
            $email = $user_data['email'];
            $siape = $user_data['numero'];
            $professor->email = $email;
            $professor->siape = $siape;
        }

        $statusOptions = self::STATUS_LABELS;
        $this->set(compact('professor', 'statusOptions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $professor = $this->Professores->get($id);
        $professor->status = $this->canonicalStatus((string)$professor->status);

        try {
            $this->Authorization->authorize($professor);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect('/');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $professor = $this->Professores->patchEntity($professor, $this->request->getData());
            if ($this->Professores->save($professor)) {
                $this->Flash->success(__('The professor has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The professor could not be saved. Please, try again.'));
        }

        $statusOptions = self::STATUS_LABELS;
        $this->set(compact('professor', 'statusOptions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $professor = $this->Professores->get($id, ['contain' => 'Estagiarios']);

        try {
            $this->Authorization->authorize($professor);
            if (count($professor->estagiarios) > 0) {
                $this->Flash->warning(__('O(a) professor(a) tem estagiários associados.'));

                return $this->redirect(['controller' => 'Professores', 'action' => 'view', $id]);
            }

            if ($this->Professores->delete($professor)) {
                $this->Flash->success(__('The professor has been deleted.'));
            } else {
                $this->Flash->error(__('The professor could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__('Authorization error: ' . $error->getMessage()));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Search for professors by name, CPF, SIAPE, email, or celular.
     *
     * @param string|null $nome Search term
     * @return \Cake\Http\Response|null
     */
    public function busca(?string $nome = null): ?Response
    {
        $this->Authorization->authorize($this->Professores);

        $query = $this->Professores->find()
            ->where([
                'OR' => [
                    'Professores.nome LIKE' => '%' . $nome . '%',
                    'Professores.cpf LIKE' => '%' . $nome . '%',
                    'Professores.siape LIKE' => '%' . $nome . '%',
                    'Professores.email LIKE' => '%' . $nome . '%',
                    'Professores.celular LIKE' => '%' . $nome . '%',
                ],
            ])
            ->order(['Professores.nome' => 'ASC']);

        $professores = $this->paginate($query, ['limit' => 25]);

        $this->set(compact('professores'));

        return null;
    }

    /**
     * Map status alias to canonical status.
     */
    private function canonicalStatus(string $status): string
    {
        foreach (self::STATUS_ALIASES as $canonicalStatus => $aliases) {
            if (in_array($status, $aliases, true)) {
                return $canonicalStatus;
            }
        }

        return $status;
    }
}
