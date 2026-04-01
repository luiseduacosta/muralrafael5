<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * beforeFilter method
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login', 'add']);
    }

    /**
     * paginate array
     */
    protected array $paginate = [
        'sortableFields' => [
            'id', 'email', 'Alunos.nome', 'Professores.nome', 'Supervisores.nome', 'created', 'modified',
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $user_data = ['administrador_id' => 0,'aluno_id' => 0,'professor_id' => 0,'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $contained = ['Administradores', 'Alunos', 'Professores', 'Supervisores'];

        $this->Authorization->authorize($this->Users);

        if ($user_data['administrador_id']) {
            $users = $this->Users->find('all')->contain($contained);
        } else {
            $users = $this->Authorization->applyScope($this->Users->find('all')->contain($contained));
        }
        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $contained = ['Administradores', 'Alunos', 'Supervisores', 'Professores'];

        $user = $this->Users->get($id, ['contain' => $contained]);
        try {
            $this->Authorization->authorize($user);
        } catch (ForbiddenException $error) {
            $user_session = $this->request->getAttribute('identity');
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            if ($user_session) {
                return $this->redirect(['action' => 'view', $user_session->id]);
            }

            return $this->redirect('/');
        }

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // authorize all users to add
        $this->Authorization->skipAuthorization();

        $user_session = $this->request->getAttribute('identity');

        if ($user_session) {
            $this->Flash->warning(__('Usuario ja esta logado.'));
        }

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['categoria', 'numero', 'password', 'email'],
                'accessibleFields' => ['password' => true],
            ]);

            // Verify is numero has a valid value set. It is mandatory for all of the new users except admin
            if ($this->request->getData('categoria') !== '1') {
                $numero = $this->request->getData('numero');
                if (empty($numero)) {
                    $this->Flash->error(__('O número é obrigatório para o tipo de usuário selecionado.'));

                    return $this->redirect(['action' => 'add']);
                }
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                // Update the entities with the user id and the user id with the entity id
                if ($user->categoria == 2) {
                    $aluno = $this->fetchTable('Alunos')->findByRegistro($user->numero)->first();
                    if ($aluno) {
                        $this->fetchTable('Alunos')->updateAll(['user_id' => $user->id], ['id' => $aluno->id]);
                    } else {
                        $this->Flash->error(__('Aluno(a) não cadastrado(a).'));

                        return $this->redirect(['controller' => 'Alunos', 'action' => 'add']);
                    }
                } elseif ($user->categoria == 3) {
                    $professor = $this->fetchTable('Professores')->findBySiape($user->numero)->first();
                    if ($professor) {
                        $this->fetchTable('Professores')->updateAll(['user_id' => $user->id], ['id' => $professor->id]);
                    } else {
                        $this->Flash->error(__('Professor(a) não cadastrado(a).'));

                        return $this->redirect(['controller' => 'Professores', 'action' => 'add']);
                    }
                } elseif ($user->categoria == 4) {
                    $supervisor = $this->fetchTable('Supervisores')->findByCress($user->numero)->first();
                    if ($supervisor) {
                        $this->fetchTable('Supervisores')->updateAll(
                            ['user_id' => $user->id],
                            ['id' => $supervisor->id],
                        );
                    } else {
                        $this->Flash->error(__('Supervisor(a) não cadastrado(a).'));

                        return $this->redirect(['controller' => 'Supervisores', 'action' => 'add']);
                    }
                } elseif ($user->categoria == 1) {
                    $administrador = $this->fetchTable('Administradores')->findByEmail($user->email)->first();
                    if ($administrador) {
                        $this->fetchTable('Administradores')->updateAll(
                            ['user_id' => $user->id],
                            ['id' => $administrador->id],
                        );
                    } else {
                        $this->Flash->error(__('Administrador(a) não cadastrado(a).'));

                        return $this->redirect(['controller' => 'Administradores', 'action' => 'add']);
                    }
                }

                return $this->redirect(['action' => 'view', $user->id]);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }

        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editpassword(?string $id = null)
    {
        return $this->edit($id);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $user_data = ['administrador_id' => 0,'aluno_id' => 0,'professor_id' => 0,'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $user = $this->Users->get($id);
        $sameUser = ($user_session && $user_session->get('id') == $id);

        try {
            $this->Authorization->authorize($user);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            if ($user_session) {
                return $this->redirect(['action' => 'edit', $user_session->id]);
            }

            return $this->redirect('/');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $opt = ['fields' => ['email']];
            $data = $this->request->getData();

            if (array_key_exists('password', $data)) {
                $opt = [
                    'fields' => ['email', 'password'],
                    'accessibleFields' => ['password' => ($user_data['administrador_id'] || $sameUser)],
                ];
            } else {
                unset($data['password']);
            }

            $user = $this->Users->patchEntity($user, $data, $opt);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }

        $user->password = '';
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        try {
            $this->Authorization->authorize($user);
            if ($this->Users->delete($user)) {
                $this->Flash->success(__('The user has been deleted.'));
            } else {
                $this->Flash->error(__('The user could not be deleted. Please, try again.'));
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(__('Authorization error: ' . $error->getMessage()));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful login, renders view otherwise.
     */
    public function login()
    {
        // authorize all users to access login
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();

        // If the user is logged in send them away.
        if ($result->isValid()) {
            $user = $result->getData();
            $this->Flash->success(__('Usuário logado.'));
            // Redirect based on category
            switch ($user['categoria']) {
                case 1: // Admin
                    $administrador = $this->fetchTable('Administradores')->findByUserId($user['id'])->first();
                    if ($administrador) {
                        if ($administrador->user_id == $user['id']) {
                            return $this->redirect([
                                'controller' => 'Administradores',
                                'action' => 'view',
                                $administrador->id,
                            ]);
                        } else {
                            // Update administrador with the user->id
                            $administrador->user_id = $user['id'];
                            $this->fetchTable('Administradores')->save($administrador);
                            $this->Flash->success(__('Administrador e usuário associados.'));

                            return $this->redirect([
                                'controller' => 'Administradores',
                                'action' => 'view',
                                $administrador->id,
                            ]);
                        }
                    }

                    return $this->redirect(['controller' => 'Administradores', 'action' => 'add']);
                case '2':
                    if ($user['numero']) {
                        try {
                            $aluno = $this->fetchTable('Alunos')->findByRegistro($user['numero'])->first();
                        } catch (RecordNotFoundException $error) {
                            $this->Flash->error(__('Record not found: ' . $error->getMessage()));

                            return $this->redirect(['controller' => 'Alunos', 'action' => 'add']);
                        }
                        if ($aluno) {
                            if ($aluno->user_id !== $user['id']) {
                                // Update aluno with the user->id
                                $aluno->user_id = $user['id'];
                                $this->fetchTable('Alunos')->save($aluno);
                                $this->Flash->success(__('Aluno e usuário associados.'));
                            }

                            return $this->redirect(['controller' => 'Alunos', 'action' => 'view', $aluno->id]);
                        }
                    }

                    return $this->redirect(['controller' => 'Alunos', 'action' => 'add']);

                case '3': // Professor: two ways to pair the user with a professor: professor_id or siape
                    if ($user['numero']) {
                        try {
                            $professor = $this->fetchTable('Professores')->findBySiape($user['numero'])->first();
                        } catch (RecordNotFoundException $error) {
                            $this->Flash->error(__('Record not found: ' . $error->getMessage()));

                            return $this->redirect(['controller' => 'Professores', 'action' => 'add']);
                        }
                        if ($professor) {
                            if ($professor->user_id !== $user['id']) {
                            // Update professor with the user->id
                                $professor->user_id = $user['id'];
                                $this->fetchTable('Professores')->save($professor);
                                $this->Flash->success(__('Professor e usuário associados.'));
                            }
                        }

                        return $this->redirect(['controller' => 'Professores', 'action' => 'view', $professor->id]);
                    }

                    return $this->redirect(['controller' => 'Professores', 'action' => 'add']);
                case '4': // Supervisor: two ways to pair the user with a supervisor: supervisor_id or cress
                    if ($user['numero']) {
                        try {
                            $supervisor = $this->fetchTable('Supervisores')->findByCress($user['numero'])->first();
                        } catch (RecordNotFoundException $error) {
                            $this->Flash->error(__('Record not found: ' . $error->getMessage()));

                            return $this->redirect(['controller' => 'Supervisores', 'action' => 'add']);
                        }
                        if ($supervisor) {
                            if ($supervisor->user_id !== $user['id']) {
                                // Update supervisor with the user->id
                                $supervisor->user_id = $user['id'];
                                $this->fetchTable('Supervisores')->save($supervisor);
                                $this->Flash->success(__('Supervisor e usuário associados.'));
                            }

                            return $this->redirect([
                                'controller' => 'Supervisores',
                                'action' => 'view',
                                $supervisor->id,
                            ]);
                        }
                    }

                    return $this->redirect(['controller' => 'Supervisores', 'action' => 'add']);
                default:
                    return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
            }
        }
        if ($this->request->is('post')) {
            $this->Flash->error('Invalid username or password');
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void Redirects on logout.
     */
    public function logout()
    {
        // authorize all users to access logout
        $this->Authorization->skipAuthorization();

        // Clear impersonation data if logging out
        $this->request->getSession()->delete('Auth.impersonating');

        $this->Authentication->logout();

        $this->Flash->warning(__('Usuario desconectado.'));

        return $this->redirect('/');
    }

    /**
     * Alternarusuario method
     * https://book.cakephp.org/authentication/3/en/impersonation.html
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful impersonation.
     */
    public function alternarusuario(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        $identity = $this->Authentication->getIdentity();
        $user_data = $identity->getOriginalData();

        // Only administrators can impersonate
        if ($user_data['categoria'] !== '1' && !$this->request->getSession()->check('Auth.impersonating')) {
            $this->Flash->error(__('Acesso negado. Apenas administradores podem alternar usuários.'));

            return $this->redirect(['action' => 'index']);
        }

        if ($id) {
            // Start impersonating
            $targetUser = $this->Users->get($id);

            // Store original admin ID if not already impersonating
            if (!$this->request->getSession()->check('Auth.impersonating')) {
                $this->request->getSession()->write('Auth.impersonating', $user_data['id']);
            }

            $this->Authentication->impersonate($targetUser);
            $this->Flash->success(__('Você agora está acessando como ' . $targetUser->email));

            return $this->redirect('/');
        } else {
            // Stop impersonating if no ID is provided and we are currently impersonating
            if ($this->request->getSession()->check('Auth.impersonating')) {
                $originalId = $this->request->getSession()->read('Auth.impersonating');
                $originalUser = $this->Users->get($originalId);

                $this->Authentication->impersonate($originalUser);
                $this->request->getSession()->delete('Auth.impersonating');

                $this->Flash->success(__('Identidade restaurada para administrador.'));

                return $this->redirect(['action' => 'index']);
            }
        }

        return $this->redirect(['action' => 'index']);
    }
}
