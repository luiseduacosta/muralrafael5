<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\Exception\ForbiddenException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;

/**
 * Estagiarios Controller
 *
 * @property \App\Model\Table\EstagiariosTable $Estagiarios
 * @method \App\Model\Entity\Estagiario[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EstagiariosController extends AppController
{
    /**
     * paginate array
     */
    protected array $paginate = [
        "sortableFields" => [
            "id",
            "Alunos.nome",
            "registro",
            "Turnos.turno",
            "nivel",
            "Instituicoes.instituicao",
            "Supervisores.nome",
            "Professores.nome",
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $periodo = $this->getRequest()->getParam("pass")
            ? $this->request->getParam("pass")[0]
            : $this->fetchTable("Configuracoes")->find()->first()[
                "mural_periodo_atual"
            ];
        $this->set("periodo", $periodo);

        $contained = [
            "Alunos",
            "Professores",
            "Supervisores",
            "Instituicoes",
            "Turmas",
        ];

        $conditions = ["conditions" => ["Estagiarios.periodo" => $periodo]];

        try {
            $this->Authorization->authorize($this->Estagiarios);
            if ($periodo == "all") {
                $estagiarios = $this->Estagiarios
                    ->find("all")
                    ->contain($contained);
            } else {
                $estagiarios = $this->Estagiarios
                    ->find("all", $conditions)
                    ->contain($contained);
            }
        } catch (ForbiddenException $error) {
            if ($periodo == "all") {
                $estagiarios = $this->Authorization->applyScope(
                    $this->Estagiarios->find("all")->contain($contained),
                );
            } else {
                $estagiarios = $this->Authorization->applyScope(
                    $this->Estagiarios
                        ->find("all", $conditions)
                        ->contain($contained),
                );
            }
        }

        $this->set("estagiarios", $this->paginate($estagiarios));

        $periodototal = $this->Estagiarios->find("list", [
            "keyField" => "periodo",
            "valueField" => "periodo",
        ]);
        $periodos = $periodototal->toArray();
        $periodos = array_merge($periodos, ["all" => "Todos"]);
        $periodos = array_reverse($periodos);

        $this->set("periodos", $periodos);
    }

    /**
     * View method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $estagiario = $this->Estagiarios->get($id, [
            "contain" => [
                "Alunos",
                "Instituicoes",
                "Supervisores",
                "Professores",
                "Turmas",
                "Complementos",
            ],
        ]);

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error("Authorization error: " . $error->getMessage());
            return $this->redirect("/");
        }

        $this->set(compact("estagiario"));
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

        $estagiario = $this->Estagiarios->newEmptyEntity();

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error("Authorization error: " . $error->getMessage());
            return $this->redirect(["controller" => "Muralestagios", "action" => "index"]);
        }

        $configuracoes = $this->fetchTable("Configuracoes");
        $periodoatual = $configuracoes
            ->find()
            ->select(["mural_periodo_atual"])
            ->first();

        $periodo = $periodoatual->mural_periodo_atual;

        $id = $this->request->getQuery('aluno_id');
        if (empty($id)) {
            $id = $user_data['aluno_id'];
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parámetros para localizar o(a) aluno(a)'));
            return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
        }

        // It is the first step of estagio (nivel 1) or a new step for this aluno
        if ($id) {
            $ultimo_estagio = $this->Estagiarios
                ->find()
                ->where(['aluno_id' => $id])
                ->order(['nivel' => 'desc'])
                ->first();

            if ($ultimo_estagio) {
                $this->Flash->success(
                    __(
                        'O aluno é estagiário ' .
                        $ultimo_estagio->nivel .
                        ' no periodo ' .
                            $ultimo_estagio->periodo,
                    ),
                );
                $nivel = $ultimo_estagio->nivel + 1;

                $ajuste2020 = $ultimo_estagio->ajuste2020;
                // Ajusta o nível de acordo com o ajuste 2020
                if ($ajuste2020 == 1) {
                    if ($nivel > 3) {
                        $nivel = 9;
                    }
                } elseif ($ajuste2020 == 0) {
                    if ($nivel > 4) {
                        $nivel = 9;
                    }
                }

                // Check period validity. Same periodo means a edit not a new step
                if ($ultimo_estagio->periodo >= $periodoatual->mural_periodo_atual) {
                    $this->Flash->error(
                        __(
                            'O período de estágio do aluno tem que ser igual ou maior que o período atual ' . $periodoatual->mural_periodo_atual,
                        ),
                    );

                    return $this->redirect([
                        'controller' => 'Estagiarios',
                        'action' => 'view',
                        $ultimo_estagio->id,
                    ]);
                }
            } else {
                $this->Flash->success(__('O aluno ainda não é estagiário'));
                $nivel = 1;
            }

            $this->set('nivel', $nivel);

            if ($this->request->is("post")) {
                // Verifica se o estagiario já existe no periodo atual
                $configuracoes = $this->fetchTable("Configuracoes")
                    ->find()
                    ->select(["mural_periodo_atual"])
                    ->first();

                $estagiarioexiste = $this->Estagiarios->find()
                    ->where([
                        "periodo" => $configuracoes->mural_periodo_atual,
                        "aluno_id" => $this->request->getData("aluno_id"),
                    ])
                    ->first();

                if ($estagiarioexiste) {
                    $this->Flash->warning("Estagiario já existe para este periodo.");
                    return $this->redirect(["action" => "view", $estagiarioexiste->id]);
                }
                $estagiario = $this->Estagiarios->patchEntity($estagiario, $this->request->getData());
                if ($this->Estagiarios->save($estagiario)) {
                    $this->Flash->success(__("Estagiario salvo com sucesso."));
                    return $this->redirect(["action" => "view", $estagiario->id]);
                }
                $this->Flash->error(
                    __("Ocorreu um erro ao salvar o estagiario. Por favor, tente novamente."),
                );
            }

            $aluno = $this->fetchTable("Alunos")->find()->where(['id' => $id])->first();
            $instituicoes = $this->fetchTable("Instituicoes")->find("list");

            if (!empty($estagiario->instituicao_id)) {
                $supervisores = $this->fetchTable("Supervisores")->find("list")->matching('Instituicoes', function ($q) use ($estagiario) {
                    return $q->where(['Instituicoes.id' => $estagiario->instituicao_id]);
                });
            } else {
                $supervisores = $this->fetchTable("Supervisores")->find("list");
            }
            $professores = $this->fetchTable("Professores")->find("list");
            $turmas = $this->fetchTable("Turmas")->find("list");

            $this->set(
                compact(
                    "periodo",
                    "estagiario",
                    "aluno",
                    "instituicoes",
                    "supervisores",
                    "professores",
                    "turmas",
                ),
            );
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $estagiario = $this->Estagiarios->get($id);

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error("Authorization error: " . $error->getMessage());
            return $this->redirect(["controller" => "Muralestagios", "action" => "index"]);
        }

        if ($this->request->is(["patch", "post", "put"])) {
            $estagiario = $this->Estagiarios->patchEntity(
                $estagiario,
                $this->request->getData(),
            );
            if ($this->Estagiarios->save($estagiario)) {
                $this->Flash->success(__("The estagiario has been edited successfully."));
                return $this->redirect(["action" => "view", $id]);
            }
            $this->Flash->error(
                __("The estagiario could not be saved. Please, try again."),
            );
        }

        $alunos = $this->Estagiarios->Alunos->find("list");
        $instituicoes = $this->Estagiarios->Instituicoes->find("list");
        if (!empty($estagiario->instituicao_id)) {
            $supervisores = $this->Estagiarios->Supervisores->find("list")->matching('Instituicoes', function ($q) use ($estagiario) {
                return $q->where(['Instituicoes.id' => $estagiario->instituicao_id]);
            });
        } else {
            $supervisores = $this->Estagiarios->Supervisores->find("list");
        }

        $professores = $this->Estagiarios->Professores->find("list");
        $turmas = $this->Estagiarios->Turmas->find("list");

        $this->set(
            compact(
                "estagiario",
                "alunos",
                "instituicoes",
                "supervisores",
                "professores",
                "turmas",
            ),
        );
    }

    /**
     * Delete method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(["post", "delete"]);

        $estagiario = $this->Estagiarios->get($id, ['contain' => 'Folhadeatividades']);

        try {
            $this->Authorization->authorize($estagiario);

            if (sizeof($estagiario->folhadeatividades) > 0) {
                $this->Flash->warning(__('Estagiário com atividades associadas'));
                return $this->redirect(['controller' => 'Estagiarios', 'action' => 'view', $id]);
            }

            if ($this->Estagiarios->delete($estagiario)) {
                $this->Flash->success(__("The estagiario has been deleted."));
            } else {
                $this->Flash->error(
                    __(
                        "The estagiario could not be deleted. Please, try again.",
                    ),
                );
            }
        } catch (ForbiddenException $error) {
            $this->Flash->error(
                __("Authorization error: " . $error->getMessage()),
            );
        }

        return $this->redirect(["action" => "index"]);
    }

    /**
     * Termocompromisso method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function termocompromisso(?string $id = null)
    {
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        $this->Authorization->skipAuthorization();

        if (isset($user_data) && $user_data['categoria'] == '2') {
            $aluno_id = $user_data['aluno_id'];
        }

        if (!isset($aluno_id) || $aluno_id === null) {
            $this->Flash->error(__('Selecionar o(a) aluno(a) para o termo de compromisso'));
            return $this->redirect(['controller' => 'Alunos', 'action' => 'index']);
        }

        $estagiario = $this->Estagiarios
            ->find()
            ->where(['aluno_id' => $aluno_id])
            ->order(['nivel' => 'desc'])
            ->first();

        if (empty($estagiario)) {
            $this->Flash->error(__('Aluno sem estágio.'));
            return $this->redirect(['controller' => 'Estagiarios', 'action' => 'add', '?' => ['aluno_id' => $aluno_id]]);
        }

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $e) {
            $this->Flash->error(__('Acesso negado. Você não tem permissão para acessar esta página.'));
            return $this->redirect(['controller' => 'Muralestagios', 'action' => 'index']);
        }

        if ($estagiario) {
            $configuracoes = $this->fetchTable('Configuracoes')
                ->find()
                ->select('mural_periodo_atual')
                ->first();
            $periodoatual = $configuracoes->mural_periodo_atual;

            // Verifica se o periodo do estagiario eh o mesmo do periodo atual
            if ($estagiario->periodo == $periodoatual) {
                return $this->redirect([
                    'action' => 'edit',
                    $estagiario->id,
                ]);
            } else {
                return $this->redirect([
                    'action' => 'add',
                    '?' => ['aluno_id' => $aluno_id],
                ]);
            }
        } else {
            $this->Flash->success(__('O(a) aluno(a) ainda não é estagiário'));

            return $this->redirect([
                'action' => 'add',
                '?' => ['aluno_id' => $aluno_id],
            ]);
        }
    }

    /**
     * Termocompromissopdf method
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function termocompromissopdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();
        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        if (empty($id)) {
            if ($user_data['categoria'] == '2') {
                $id = $user_data['aluno_id'];
            }
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parâmetros para localizar o estagiário'));
            return $this->redirect(['action' => 'index']);
        }

        try {
            $estagiario = $this->Estagiarios->get($id, [
                'contain' => ['Alunos', 'Supervisores', 'Instituicoes'],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Estagiário não encontrado.'));
            return $this->redirect(['action' => 'index']);
        }

        $configuracao = $this->fetchTable('Configuracoes')
            ->find()
            ->where(['Configuracoes.id' => 1])
            ->first();

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
            'download' => true,
            'filename' => 'termo_de_compromisso_' . $id . '.pdf',
        ]);
        $this->set('configuracao', $configuracao);
        $this->set('estagiario', $estagiario);
    }

    /**
     * Declaracaodeestagiopdf method
     *
     * @param string|null $id Estagiario id.
     */
    public function declaracaodeestagiopdf(?string $id = null)
    {
        $this->Authorization->skipAuthorization();

        $user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0];
        $user_session = $this->request->getAttribute('identity');
        if ($user_session) {
            $user_data = $user_session->getOriginalData();
        }

        if (empty($id)) {
            if ($user_data['categoria'] == '2') {
                $id = $user_data['aluno_id'];
            }
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parâmetros para localizar o estagiário'));
            return $this->redirect(['action' => 'index']);
        }

        $estagiario = $this->Estagiarios
            ->find()
            ->contain(["Alunos", "Supervisores", "Instituicoes"])
            ->where(["Estagiarios.id IS" => $id])
            ->first();

        if (!$estagiario) {
            $this->Flash->error(__("Sem estagio cadastrado."));
            return $this->redirect([
                "controller" => "estagiarios",
                "action" => "view",
                $id,
            ]);
        }

        if (empty($estagiario->aluno->identidade)) {
            $this->Flash->error(__("Aluno sem RG"));
            return $this->redirect(
                "/alunos/view/" . $estagiario->aluno->id,
            );
        }

        if (empty($estagiario->aluno->orgao)) {
            $this->Flash->error(
                __("Aluno não especifica o orgão emisor do documento"),
            );
            return $this->redirect(
                "/alunos/view/" . $estagiario->aluno->id,
            );
        }
        if (empty($estagiario->aluno->cpf)) {
            $this->Flash->error(__("Aluno sem CPF"));
            return $this->redirect(
                "/alunos/view/" . $estagiario->aluno->id,
            );
        }

        if (empty($estagiario->supervisor->id)) {
            $this->Flash->error(__("Falta o supervisor de estágio"));
            return $this->redirect("/estagiarios/view/" . $estagiario->id);
        }

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName("CakePdf.Pdf");
        $this->viewBuilder()->setOption("pdfConfig", [
            "orientation" => "portrait",
        ]);
        $this->set("estagiario", $estagiario);
    }

    /**
     * Selecionasupervisores method. It is used in add and edit method by AJAX.
     * Seleciona os supervisores da instituicao_id
     *
     * @param string|null $id Estagiario id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     *
     */
    private function selecionasupervisores($instituicao_id = null)
    {
        $supervisoresinstituicao = null;
        if ($instituicao_id) {
            $supervisoresDaInstituicao = $this->Estagiarios->Instituicoes
                ->find()
                ->contain(["Supervisores"])
                ->where(["Instituicoes.id" => $instituicao_id])
                ->first();

            if ($supervisoresDaInstituicao) {
                foreach (
                    $supervisoresDaInstituicao->supervisores
                    as $supervisor
                ) {
                    $supervisoresinstituicao[$supervisor["id"]] =
                        $supervisor["nome"];
                }
            } else {
                $supervisoresinstituicao[0] = "Sem supervisor(a)";
                $supervisoresinstituicao[0] = "Sem dados";
            }
        }
        return $supervisoresinstituicao;
    }

    /**
     * Nivelestagio method
     *
     * Compara o periodoautal com o periodo de estagio do estagiario para definiar o nivel de estagio
     * @param string $periodoatual Periodo atual
     * @param Estagiario $ultimoestagio Ultimo estagio
     * @return int Nivel de estagio
     */
    private function nivelestagio($periodoatual, $ultimoestagio)
    {
        /* Se o periodo atual é o mesmo do periodo cadastrado no estagiário deixa o nivel como está */
        if ($periodoatual == $ultimoestagio->periodo) {
            $nivel = $ultimoestagio->nivel;
            /** Se o periodo atual é maior que o cadastrado então passa para o próximo nivel e insere um novo registro */
        } elseif ($periodoatual > $ultimoestagio->periodo) {
            $nivel = $ultimoestagio->nivel + 1;
            /** Calculo o ultimo nível de estágio possível a partir do ajuste curricular. */
            if ($ultimoestagio->ajuste2020 == 1) {
                $ultimo_nivel_curricular = 3;
            } else {
                $ultimo_nivel_curricular = 4;
            }
            /** Se nivel é maior que o ultimo nivel curricular então está realizando estagio extracurricular e o nivel é 9. */
            if ($nivel > $ultimo_nivel_curricular) {
                // Estágio não curricular
                $nivel = 9;
            }
        } else {
            $this->Flash->error(
                __(
                    "Período de estágio atual não pode ser menor que o último período cursado.",
                ),
            );
            return $this->redirect([
                "action" => "view",
                $ultimoestagio->id,
            ]);
        }
        return $nivel;
    }


}
