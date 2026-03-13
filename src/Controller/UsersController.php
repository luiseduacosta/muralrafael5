<?php

declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
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
            'id', 'email', 'Alunos.nome', 'Professores.nome', 'Supervisores.nome', 'created', 'modified'
        ]
    ];
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }

        $contained = []; //'Administradores', 'Alunos', 'Professores', 'Supervisores'];
        
        $this->Authorization->authorize($this->Users);
        
        if ($user_data['administrador_id']) {
            $query = $this->Users->find('all')->contain($contained);
        } else {
            $query = $this->Authorization->applyScope($this->Users->find('all')->contain($contained));
        }
        $users = $this->paginate($query);
        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contained = ['Administradores', 'Alunos', 'Supervisores', 'Professores'];
        
        $user = $this->Users->get($id, [ 'contain' =>  $contained ]);
        
        try {
            $this->Authorization->authorize($user);
        } catch (ForbiddenException $error) {
            $user_session = $this->request->getAttribute('identity');
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['action' => 'view', $user_session->id]);
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
        
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }

        if ($user_session) {
            $this->Flash->warning(__('Usuario ja esta logado.'));
        }
        
        $user = $this->Users->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['categoria', 'numero', 'password', 'email'],
                'accessibleFields' => ['password' => true]
            ]);
 
            // Verfify is numero has a valid value set. It is mandatory for all of the new users except admin
            if ($this->request->getData('categoria') != '1') {
                $numero = $this->request->getData('numero');
                if (empty($numero)) {
                    $this->Flash->error(__('O número é obrigatório para o tipo de usuário selecionado.'));
                } else {
                    if ($this->request->getData('categoria') == '2') {
                        $aluno = $this->fetchTable('Alunos')->findByRegistro($numero)->first();
                        if ($aluno) {
                            $user->aluno_id = $aluno->id;
                            $user->numero = $aluno->registro;
                        } else {
                            $user->numero = $numero;
                        }
                    } elseif ($this->request->getData('categoria') == '3') {
                        $professor = $this->fetchTable('Professores')->findBySiape($numero)->first();
                        if ($professor) {
                            $user->professor_id = $professor->id;
                            $user->numero = $professor->siape;
                        }
                    } elseif ($this->request->getData('categoria') == '4') {
                        $supervisor = $this->fetchTable('Supervisores')->findByCress($numero)->first();
                        if ($supervisor) {
                            $user->supervisor_id = $supervisor->id;
                            $user->numero = $supervisor->cress;
                        }
                    }
                }
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                // Update the entity with the user id
                if ($user->categoria == '2') {
                    $aluno = $this->fetchTable('Alunos')->findByRegistro($user->numero)->first();
                    if ($aluno) {
                        $this->fetchTable('Alunos')->updateAll(['user_id' => $user->id], ['id' => $aluno->id]);
                    }
                } else if ($user->categoria == '3') {
                    $professor = $this->fetchTable('Professores')->findBySiape($user->numero)->first();
                    if ($professor) {
                        $this->fetchTable('Professores')->updateAll(['user_id' => $user->id], ['id' => $professor->id]);
                    }
                } else if ($user->categoria == '4') {
                    $supervisor = $this->fetchTable('Supervisores')->findByCress($user->numero)->first();
                    if ($supervisor) {
                        $this->fetchTable('Supervisores')->updateAll(['user_id' => $user->id], ['id' => $supervisor->id]);
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
    public function editpassword($id = null)
    {
        $this->edit($id);
    }
    public function edit($id = null)
    {
        $user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) { $user_data = $user_session->getOriginalData(); }

        $user = $this->Users->get($id);
        $sameUser = ($user_session and $user_session->get('id') == $id);
                
        try {
            $this->Authorization->authorize($user);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());
            return $this->redirect(['action' => 'edit', $user_session->id]);
        }
            
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $opt = ['fields' => ['email']];
            $data = $this->request->getData();
            
            if (array_key_exists('password', $data)) {
                $opt = [
                    'fields' => ['email', 'password'],
                    'accessibleFields' => ['password' => ($user_data['administrador_id'] || $sameUser)]
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
    public function delete($id = null) 
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
            $this->Flash->error(__( 'Authorization error: ' . $error->getMessage() ));
        }

        return $this->redirect(['action' => 'index']);
    }

    /*
     * Login method
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
                case '1': // Admin
                    return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
                case '2': // Aluno
                    $aluno = $this->fetchTable('Alunos')->findByRegistro($user['numero'])->first();
                    if ($aluno) {
                        if ($aluno->user_id == $user['id']) {
                            return $this->redirect(['controller' => 'Alunos', 'action' => 'view', $aluno->id]);
                        } else {
                            // Update user->aluno_id with the aluno->id
                            $user->aluno_id = $aluno->id;
                            $this->Users->save($user);
                            // Update aluno with the user->id
                            $aluno->user_id = $user['id'];
                            $this->fetchTable('Alunos')->save($aluno);
                            $this->Flash->success(__('Aluno e usuário associados.'));
                            return $this->redirect(['controller' => 'Alunos', 'action' => 'view', $aluno->id]);
                        }
                    }
                    return $this->redirect(['controller' => 'Alunos', 'action' => 'add']);
                case '3': // Professor
                    $professor = $this->fetchTable('Professores')->findBySiape($user['numero'])->first();
                    if ($professor) {
                        if ($professor->user_id == $user['id']) {
                            return $this->redirect(['controller' => 'Professores', 'action' => 'view', $professor->id]);
                        } else {
                            // Update user->professor_id with the professor->id
                            $user->professor_id = $professor->id;
                            $this->Users->save($user);
                            // Update professor with the user->id
                            $professor->user_id = $user['id'];
                            $this->fetchTable('Professores')->save($professor);
                            $this->Flash->success(__('Professor e usuário associados.'));
                            return $this->redirect(['controller' => 'Professores', 'action' => 'view', $professor->id]);
                        }
                    }
                    return $this->redirect(['controller' => 'Professores', 'action' => 'add']);
                case '4': // Supervisor
                    $supervisor = $this->fetchTable('Supervisores')->findByCress($user['numero'])->first();
                    if ($supervisor) {
                        if ($supervisor->user_id == $user['id']) {
                            return $this->redirect(['controller' => 'Supervisores', 'action' => 'view', $supervisor->id]);
                        } else {
                            // Update user->supervisor_id with the supervisor->id
                            $user->supervisor_id = $supervisor->id;
                            $this->Users->save($user);
                            // Update supervisor with the user->id
                            $supervisor->user_id = $user['id'];
                            $this->fetchTable('Supervisores')->save($supervisor);
                            $this->Flash->success(__('Supervisor e usuário associados.'));
                            return $this->redirect(['controller' => 'Supervisores', 'action' => 'view', $supervisor->id]);
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

    /*
     * Logout method
     */
    public function logout()
    {
        // authorize all users to access logout
        $this->Authorization->skipAuthorization();
        
        $this->Authentication->logout();
        
        $this->Flash->warning(__('Usuario desconectado.'));
        return $this->redirect('/');
    }

    /*
     * Alternarusuario method
     * https://book.cakephp.org/authentication/3/en/impersonation.html
     */
    public function alternarusuario()
    {
        $this->Authorization->skipAuthorization();

        // TODO: Implement user impersonation logic
        $this->Flash->warning(__('Funcionalidade não implementada.'));
        return $this->redirect(['action' => 'index']);
    }
    
}
