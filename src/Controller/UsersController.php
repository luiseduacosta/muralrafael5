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
class UsersController extends AppController {
    /**
     * beforeFilter method
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        // allow all visitors to access login and add
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

        $contained = [];//'Administradores', 'Alunos', 'Professores', 'Supervisores'];
        
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
                'fields' => ['password', 'email'],
                'accessibleFields' => ['password' => true]
            ]);
                
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
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
                    'accessibleFields' => ['password' => ($user_data['administrador_id'] OR $sameUser)]
                ];
            } else { unset($data['password']); }
            
            $user = $this->Users->patchEntity($user, $data, $opt);
            
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }

        $user['password'] = '';    
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
            $this->Flash->success(__('Usuario logado.'));
            
            $target = $this->Authentication->getLoginRedirect() ?? '/';
            return $this->redirect($target);
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

        // pr($this->data);
        // die();
        
 
    }
    
}
