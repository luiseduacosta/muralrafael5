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
            "Estagiarios.id",
            "Alunos.nome",
            "Estagiarios.registro",
            "Instituicoes.instituicao",
            "Supervisores.nome",
            "Estagiarios.periodo",
            "Estagiarios.nivel",
            "Estagiarios.nota",
            "Estagiarios.ch",
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $periodo = $this->request->getData('periodo')
            ?? ($this->getRequest()->getParam('pass') ? $this->request->getParam('pass')[0] : null)
            ?? $this->configuracao->termo_compromisso_periodo;
        $this->set('periodo', $periodo);

        $contained = [
            "Alunos",
            "Professores",
            "Supervisores",
            "Instituicoes",
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

        $periodoatual = $this->configuracao->termo_compromisso_periodo;

        $id = $this->request->getQuery('aluno_id');
        if (empty($id)) {
            $id = $user_data['aluno_id'];
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parámetros para localizar o(a) aluno(a)'));
            return $this->redirectBack(['controller' => 'Alunos', 'action' => 'index']);
        }

        // Querying if is the first step of estagio (nivel 1) or a new step for this aluno
        if ($id) {
            $ultimo_estagio = $this->Estagiarios
                ->find()
                ->where(['aluno_id' => $id])
                ->order(['nivel' => 'desc'])
                ->first();

            if ($ultimo_estagio) {
                $this->Flash->info(
                    __(
                        'O aluno é estagiário ' .
                        $ultimo_estagio->nivel .
                        ' no periodo ' .
                            $ultimo_estagio->periodo,
                    ),
                );
                // Go to next nivel
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

                // Check period validity. Mesmo ou maior período significa edição, não um novo passo
                $compare = $this->comparePeriodo(
                    (string)$ultimo_estagio->periodo,
                    (string)$periodoatual,
                );

                if ($compare >= 0) {
                    $this->Flash->info(
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
                $this->Flash->info(__('O aluno ainda não é estagiário'));
                $nivel = 1;
            }

            $this->set('nivel', $nivel);

            if ($this->request->is("post")) {
                // Verifica se o estagiario já existe no periodo atual
  
                $estagiarioexiste = $this->Estagiarios->find()
                    ->where([
                        "periodo" => $periodoatual,
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
            $instituicoes = $this->fetchTable("Instituicoes")->find("list")->order(['instituicao' => 'ASC']);

            if (!empty($estagiario->instituicao_id)) {
                $supervisores = $this->fetchTable("Supervisores")->find("list")->matching('Instituicoes', function ($q) use ($estagiario) {
                    return $q->where(['Instituicoes.id' => $estagiario->instituicao_id]);
                });
            } else {
                $supervisores = $this->fetchTable("Supervisores")->find("list")->order(['nome' => 'ASC']);
            }
            $professores = $this->fetchTable("Professores")->find("list");

            $this->set(
                compact(
                    "periodo",
                    "estagiario",
                    "aluno",
                    "instituicoes",
                    "supervisores",
                    "professores",
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
        
        // Support AJAX POST with id in request body
        if ($id === null && $this->request->is(['post', 'put', 'patch'])) {
            $id = $this->request->getData('id');
        }

        $estagiario = $this->Estagiarios->get($id);

        try {
            $this->Authorization->authorize($estagiario);
        } catch (ForbiddenException $error) {
            $this->Flash->error("Authorization error: " . $error->getMessage());
            return $this->redirect(["controller" => "Muralestagios", "action" => "index"]);
        }

        if ($this->request->is(["patch", "post", "put"])) {
            // Check the format of the field nota and replace comma with dot
            if (!empty($this->request->getData('nota'))) {
                $nota = str_replace(',', '.', $this->request->getData('nota'));
                $this->request = $this->request->withData('nota', $nota);
            }

            $estagiario = $this->Estagiarios->patchEntity(
                $estagiario,
                $this->request->getData(),
            );

            if ($this->Estagiarios->save($estagiario)) {
                if ($this->request->is('ajax')) {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode(['status' => 'success', 'data' => $estagiario]));
                }
                $this->Flash->success(__('Registro de estagiario atualizado.'));

                return $this->redirect(['action' => 'view', $id]);
            }

            if ($this->request->is('ajax')) {
                 return $this->response->withStatus(400)
                    ->withType('application/json')
                    ->withStringBody(json_encode(['status' => 'error', 'errors' => $estagiario->getErrors()]));
            }

            $this->Flash->error(
                __('Registro de estagiário não foi atualizado. Tente novamente.'),
            );
        }

        $alunos = $this->fetchTable("Alunos")->find("list")->order(['nome' => 'ASC']);
        $instituicoes = $this->fetchTable("Instituicoes")->find("list")->order(['instituicao' => 'ASC']);
        if (!empty($estagiario->instituicao_id)) {
            $supervisores = $this->fetchTable("Supervisores")->find("list")->matching('Instituicoes', function ($q) use ($estagiario) {
                return $q->where(['Instituicoes.id' => $estagiario->instituicao_id]);
            });
        } else {
            $supervisores = $this->fetchTable("Supervisores")->find("list")->order(['nome' => 'ASC']);
        }
        $professores = $this->fetchTable("Professores")->find("list")->order(['nome' => 'ASC']);
        $complementos = $this->fetchTable("Complementos")->find("list");
        $this->set(
            compact(
                "estagiario",
                "alunos",
                "instituicoes",
                "supervisores",
                "professores",
                "complementos",
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

        $aluno_id = $this->request->getQuery('aluno_id');

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

            $periodoatual = $this->configuracao->termo_compromisso_periodo;

            $compare = $this->comparePeriodo((string)$periodoatual, (string)$estagiario->periodo);

            // Mesmo período: editar o estágio do período atual
            if ($compare === 0) {
                return $this->redirect([
                    'action' => 'edit',
                    $estagiario->id,
                ]);
            }

            // Período atual (configurações) posterior ao último estágio: criar novo estágio
            if ($compare === 1) {
                return $this->redirect([
                    'action' => 'add',
                    '?' => ['aluno_id' => $aluno_id],
                ]);
            }

            // Período atual anterior ao último estágio: configurações desatualizadas / dados inconsistentes
            $this->Flash->error(
                __(
                    'Período atual ({0}) não pode ser anterior ao último período de estágio ({1}).',
                    (string)$periodoatual,
                    (string)$estagiario->periodo,
                ),
            );

            return $this->redirect([
                'action' => 'edit',
                $estagiario->id,
            ]);
        } else {
            $this->Flash->success(__('O(a) aluno(a) ainda não é estagiário'));

            return $this->redirect([
                'action' => 'add',
                '?' => ['aluno_id' => $aluno_id],
            ]);
        }
    }

    /**
     * Converte um período no formato YYYY-1/2 para uma chave numérica comparável.
     *
     * @param string $periodo Ex.: "2025-1" ou "2025-2"
     * @return int Chave comparável (ano * 10 + semestre). Retorna 0 se inválido.
     */
    private function periodoKey(string $periodo): int
    {
        $periodo = trim($periodo);
        if ($periodo === '') {
            return 0;
        }

        $parts = explode('-', $periodo, 2);
        if (count($parts) !== 2) {
            return 0;
        }

        $year = (int)trim($parts[0]);
        $half = (int)trim($parts[1]);

        if ($year <= 0 || ($half !== 1 && $half !== 2)) {
            return 0;
        }

        return ($year * 10) + $half;
    }

    /**
     * Compara dois períodos no formato YYYY-1/2.
     *
     * @param string $a Primeiro período
     * @param string $b Segundo período
     * @return int Retorna 1 se $a > $b, 0 se $a == $b, -1 se $a < $b
     */
    private function comparePeriodo(string $a, string $b): int
    {
        $ka = $this->periodoKey($a);
        $kb = $this->periodoKey($b);

        if ($ka === 0 || $kb === 0) {
            return (strcmp(trim($a), trim($b)) <=> 0);
        }

        return ($ka <=> $kb);
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

        $id = $this->request->getQuery('estagiario_id');

        if ($user_data['categoria'] == '2') {
            $id = $user_data['aluno_id'];
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parâmetros para localizar o estagiário'));
            return $this->redirectBack(['action' => 'index']);
        }

        try {
            $estagiario = $this->Estagiarios->get($id, [
                'contain' => ['Alunos', 'Supervisores', 'Instituicoes'],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Estagiário não encontrado.'));
            return $this->redirectBack(['action' => 'index']);
        }

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
            'download' => true,
            'filename' => 'termo_de_compromisso_' . $id . '.pdf',
        ]);
        $this->set('configuracao', $this->configuracao);
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

        $id = $this->request->getQuery('estagiario_id');

        if ($user_data['categoria'] == '2') {
            $id = $user_data['aluno_id'];
        }

        if (empty($id)) {
            $this->Flash->error(__('Sem parâmetros para localizar o estagiário'));
            return $this->redirectBack(['action' => 'index']);
        }

        $estagiario = $this->Estagiarios
            ->find()
            ->contain(["Alunos", "Supervisores", "Instituicoes"])
            ->where(["Estagiarios.id IS" => $id])
            ->first();

        if (!$estagiario) {
            $this->Flash->error(__("Sem estagio cadastrado."));
            return $this->redirectBack([
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

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName("CakePdf.Pdf");
        $this->viewBuilder()->setOption("pdfConfig", [
            "orientation" => "portrait",
        ]);
        $this->set("estagiario", $estagiario);
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

    /**
     * lancanota method
     *
     * @param string|null $id Professor id.
     * @return \Cake\Http\Response|null|void Renders view otherwise.
     */
    public function lancanota($id = null)
    {
        $this->Authorization->authorize($this->Estagiarios, 'lancanota');

        $user_session = $this->request->getAttribute('identity');
        $user_data = $user_session->getOriginalData();

        if ($user_data['categoria'] == '3') {
            $professor_id = $user_data['professor_id'];
        } else {
            $professor_id = (int)$this->request->getQuery("professor_id") ?? $this->request->getData("professor_id");
        }

        if (empty($professor_id)) {
            $this->Flash->error(
                __(
                    "Sem parâmetro para localizar os estagiários do(a) professor(a).",
                ),
            );
            return $this->redirectBack(["action" => "index"]);
        }

        $professor = $this->fetchTable('Professores')
            ->find()
            ->select(["id", "nome"])
            ->where(["id" => $professor_id])
            ->first();

        if (!$professor) {
            $this->Flash->error(__("Professor não encontrado."));
            return $this->redirectBack(["action" => "index"]);
        }

        $periodos = $this->Estagiarios->find("list", ["keyField" => "periodo", "valueField" => "periodo"])
            ->where(["Estagiarios.professor_id" => $professor_id])
            ->order(["Estagiarios.periodo" => "ASC"])
            ->toArray();

        $periodo = $this->request->getQuery("periodo") ?? $this->request->getData("periodo");
    
        if ($periodo === null) {
            $periodo = !empty($periodos) ? end($periodos) : null;
        }

        $estagiariosQuery = $this->Estagiarios->find()
            ->contain([
                    "Alunos" => [
                        "fields" => ["id", "nome"],
                    ],
                    "Professores" => ["fields" => ["id", "nome", "siape"]],
                    "Supervisores" => ["fields" => ["id", "nome"]],
                    "Instituicoes" => ["fields" => ["id", "instituicao"]],
                    "Avaliacoes" => ["fields" => ["id", "estagiario_id"]],
            ])
            ->where(["Estagiarios.professor_id" => $professor_id]);

        if ($periodo) {
            $estagiariosQuery->where(["Estagiarios.periodo" => $periodo]);
        }

        $estagiarios = $this->paginate($estagiariosQuery, [
            "sortableFields" => [
                "Estagiarios.id",
                "Alunos.nome",
                "Estagiarios.registro",
                "Instituicoes.instituicao",
                "Supervisores.nome",
                "Estagiarios.periodo",
                "Estagiarios.nivel",
                "Estagiarios.nota",
                "Estagiarios.ch",
            ],
            "order" => ["Alunos.nome" => "ASC"],
        ]);

        $this->set("periodo", $periodo);
        $this->set("periodos", $periodos);
        $this->set("professor", $professor);
        $this->set("estagiarios", $estagiarios);
    }

    /**
     * Lancanotapdf method - PDF export of lancanota
     *
     * @return \Cake\Http\Response|null|void
     */
    public function lancanotapdf()
    {
        $this->Authorization->authorize($this->Estagiarios, 'lancanotapdf');

        $user_session = $this->request->getAttribute('identity');
        $user_data = $user_session->getOriginalData();

        if ($user_data['categoria'] == '3') {
            $professor_id = $user_data['professor_id'];
        } else {
            $professor_id = (int)$this->request->getQuery("professor_id");
        }

        if (empty($professor_id)) {
            $this->Flash->error(
                __(
                    "Sem parâmetro para localizar os estagiários do(a) professor(a).",
                ),
            );
            return $this->redirectBack(["action" => "index"]);
        }

        $professor = $this->fetchTable('Professores')
            ->find()
            ->select(["id", "nome"])
            ->where(["id" => $professor_id])
            ->first();

        if (!$professor) {
            $this->Flash->error(__("Professor não encontrado."));
            return $this->redirectBack(["action" => "index"]);
        }

        $periodo = $this->request->getQuery("periodo");

        $estagiariosQuery = $this->Estagiarios->find()
            ->contain([
                "Alunos" => [
                    "fields" => ["id", "nome"],
                ],
                "Professores" => ["fields" => ["id", "nome", "siape"]],
                "Supervisores" => ["fields" => ["id", "nome"]],
                "Instituicoes" => ["fields" => ["id", "instituicao"]],
                "Avaliacoes" => ["fields" => ["id", "estagiario_id"]],
            ])
            ->where(["Estagiarios.professor_id" => $professor_id])
            ->order(["Alunos.nome" => "ASC"]);

        if ($periodo) {
            $estagiariosQuery->where(["Estagiarios.periodo" => $periodo]);
        }

        $estagiarios = $estagiariosQuery->all();

        $this->viewBuilder()->setLayout('pdf/default');
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $this->viewBuilder()->setOption('pdfConfig', [
            'orientation' => 'portrait',
        ]);

        $this->set("periodo", $periodo);
        $this->set("professor", $professor);
        $this->set("estagiarios", $estagiarios);
    }

}
