<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Datasource\Exception\RecordNotFoundException;

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
     *
     * @param string|null $id Estagiario.id
     * $id = estagiario_id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index(?string $id = null)
    {
        try {
            $this->Authorization->authorize($this->Folhadeatividades);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        if (empty($estagiario_id)) {
            $this->Flash->error(__('Selecione o estagiário'));

            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'index']);
        }

        if ($estagiario_id) {
            $folhadeatividades = $this->Folhadeatividades
                ->find()
                ->contain(['Estagiarios' => ['Alunos', 'Instituicoes', 'Supervisores']])
                ->find('all')
                ->order(['Folhadeatividades.id'])
                ->where(['estagiario_id' => $estagiario_id]);

            $estagiario = $this->fetchTable('Estagiarios')
                ->find()
                ->contain(['Alunos', 'Instituicoes', 'Supervisores', 'Professores', 'Folhadeatividades'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        }

        if (empty($folhadeatividades)) {
            $this->Flash->error(
                __('Selecione o estagiário e o período da folha de atividades'),
            );
        }
        $folhadeatividades = $this->paginate($folhadeatividades);

        $this->set(compact('estagiario', 'folhadeatividades'));
    }

    /**
     * Add method
     *
     * @param string|null $id Estagiario.id
     * $id = estagiario_id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function add(?string $id = null)
    {
        try {
            $this->Authorization->authorize($this->Folhadeatividades);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        if ($estagiario_id) {
            $folhadeatividades = $this->Folhadeatividades
                ->find('all')
                ->order(['Folhadeatividades.id'])
                ->contain(['Estagiarios' => ['Alunos', 'Instituicoes']])
                ->where(['estagiario_id' => $estagiario_id]);

            $estagiario = $this->fetchTable('Estagiarios')
                ->find()
                ->contain(['Alunos', 'Instituicoes'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->first();
        }

        $folhadeatividadeentity = $this->Folhadeatividades->newEmptyEntity();

        if ($this->request->is('post')) {
            $dados = $this->request->getData();
            $dados['horario'] = null;
            $folhadeatividaderesposta = $this->Folhadeatividades->patchEntity(
                $folhadeatividadeentity,
                $dados,
            );
            if ($this->Folhadeatividades->save($folhadeatividaderesposta)) {
                $this->Flash->success(__('Atividades cadastrada!'));

                return $this->redirect([
                    'controller' => 'Folhadeatividades',
                    'action' => 'view',
                    $folhadeatividaderesposta->id,
                    ],);
            }
            $this->Flash->error(
                __('Atividade não foi cadastrada. Tente mais uma vez.'),
            );
        }
        if (!empty($folhadeatividades)) {
            $this->set('folhadeatividades', $folhadeatividades);
        }
        if (!empty($estagiario)) {
            $this->set('estagiario', $estagiario);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        if ($estagiario_id) {
            $folhadeatividade = $this->Folhadeatividades
                ->find()
                ->contain(['Estagiarios' => ['Alunos', 'Instituicoes']])
                ->where(['estagiario_id' => $estagiario_id])
                ->first();
        } else {
            $folhadeatividade = $this->Folhadeatividades->get($id, [
                'contain' => ['Estagiarios'],
            ]);
        }

        if ($folhadeatividade) {
            try {
                $this->Authorization->authorize($folhadeatividade);
            } catch (ForbiddenException $error) {
                $this->Flash->error('Authorization error: ' . $error->getMessage());

                return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
            }
        }

        if (empty($folhadeatividade)) {
            $this->Flash->error(__('Sem atividades cadastradas'));

            return $this->redirect([
                'controller' => 'folhadeatividades',
                'action' => 'add',
                '?' => ['estagiario_id' => $estagiario_id],
            ]);
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
    public function edit(?string $id = null)
    {
        $folhadeatividade = $this->Folhadeatividades->get($id, [
            'contain' => [],
        ]);

        try {
            $this->Authorization->authorize($folhadeatividade);
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $folhadeatividade = $this->Folhadeatividades->patchEntity(
                $folhadeatividade,
                $this->request->getData(),
            );
            if ($this->Folhadeatividades->save($folhadeatividade)) {
                $this->Flash->success(__('Atividade atualizada.'));

                return $this->redirect([
                    'controller' => 'Folhadeatividades',
                    'action' => 'view',
                    '?' => ['id' => $folhadeatividade->id],
                ]);
            }
            $this->Flash->error(
                __('Não foi possível atualizar. Tente outra vez.'),
            );
        }
        $estagiario = $this->Folhadeatividades
            ->find()
            ->contain(['Estagiarios' => ['Alunos', 'Instituicoes']])
            ->where(['Folhadeatividades.id' => $id])
            ->first();
        $this->set(compact('folhadeatividade', 'estagiario'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $folhadeatividade = $this->Folhadeatividades->get($id);

        try {
            $this->Authorization->authorize($folhadeatividade);
            $estagiariotabela = $this->fetchTable('Estagiarios');
            $estagiario = $estagiariotabela
                ->find()
                ->where(['id' => $folhadeatividade->estagiario_id])
                ->first();

            if ($this->Folhadeatividades->delete($folhadeatividade)) {
                $this->Flash->success(__('Registro de atividade excluido.'));

                return $this->redirect([
                    'controller' => 'estagiarios',
                    'action' => 'view',
                    $estagiario->id,
                ]);
            } else {
                $this->Flash->error(
                    __('Registro de atividade nao foi excluido. Tente novamente.'),
                );

                return $this->redirect([
                    'controller' => 'folhadeatividades',
                    'action' => 'view',
                    $id,
                ]);
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error('Authorization error: ' . $error->getMessage());

            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }
    }

    /**
     * Folhadeatividadespdf method
     *
     * Gera a folha de atividades em PDF.
     *
     * @param string|null $id Folhadeatividade id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function folhadeatividadespdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');

        if ($user_data['aluno_id'] > 0) {
            $redirect = [
                'controller' => 'alunos',
                'action' => 'view',
                $user_data['aluno_id'],
            ];
        } else {
            $redirect = [
                'controller' => 'muralestagios',
                'action' => 'index',
            ];
        }
        if (empty($estagiario_id)) {
            $this->Flash->error(__('Sem parâmetros para gerar a folha de atividades.'));

            return $this->redirect($redirect);
        }

        $estagiario = $this->Folhadeatividades->Estagiarios
            ->find()
            ->where(['id' => $estagiario_id])
            ->first();

        if (empty($estagiario)) {
            $this->Flash->error(__('Sem estagiário para gerar a folha de atividades.'));

            return $this->redirect($redirect);
        }
        if ($estagiario->aluno_id != $user_data['aluno_id'] && $user_data['aluno_id'] > 0) {
            $this->Flash->error(__('Você não tem permissão para gerar a folha de atividades.'));

            return $this->redirect($redirect);
        }

        $this->viewBuilder()->setLayout(null);
        $atividades = $this->Folhadeatividades
            ->find()
            ->contain([
                'Estagiarios' => [
                    'Alunos',
                    'Professores',
                    'Instituicoes',
                    'Supervisores',
                ],
            ])
            ->where(['Folhadeatividades.estagiario_id' => $estagiario_id])
            ->all();

        $estagiario = $this->Folhadeatividades->Estagiarios
            ->find()
            ->contain(['Alunos', 'Professores', 'Instituicoes', 'Supervisores'])
            ->where(['Estagiarios.id' => $estagiario_id])
            ->first();

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
            'download' => true,
            'filename' => 'folha_de_atividades_' . $estagiario_id . '.pdf',
        ]);

        $this->set('atividades', $atividades);
        $this->set('estagiario', $estagiario);
    }

    /**
     * Atividadesmanualpdf method - Generates PDF for activities of an intern without relying on Folhadeatividade entity
     *
     * @param string|null $id Estagiário id (unused in sig, used via query)
     * @return \Cake\Http\Response|null|void Renders PDF view
     */
    public function atividadesmanualpdf(?string $id = null)
    {
        $estagiario_id = $this->getRequest()->getQuery('estagiario_id');
        $this->Authorization->skipAuthorization();

        if ($estagiario_id == null) {
            $this->Flash->error(__('Selecione o estagiário e o período da folha de atividades'));

            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'index']);
        }

        try {
            $estagiario = $this->fetchTable('Estagiarios')->find()
                ->contain(['Alunos', 'Professores', 'Instituicoes', 'Supervisores'])
                ->where(['Estagiarios.id' => $estagiario_id])
                ->select([
                    'estagiario_id' => 'Estagiarios.id',
                    'aluno_nome' => 'Alunos.nome',
                    'aluno_registro' => 'Alunos.registro',
                    'estagiario_periodo' => 'Estagiarios.periodo',
                    'estagiario_nivel' => 'Estagiarios.nivel',
                    'supervisor_nome' => 'Supervisores.nome',
                    'supervisor_cress' => 'Supervisores.cress',
                    'instituicao_nome' => 'Instituicoes.instituicao',
                    'professor_nome' => 'Professores.nome',
                ])
                ->first();
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Estagiário(a) não localizado(a).'));

            return $this->redirect(['action' => 'index']);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'orientation' => 'portrait',
                'download' => true,
                'filename' => 'folha_de_atividades_' . $estagiario->aluno_nome . '.pdf',
            ],
        );
        $this->set('estagiario', $estagiario);
    }
}
