-- Script to update tables structure and data from old ess_apps to new mural5_com_avaliacao.sql

-- Table USERS: Add new columns and change types of existing columns, except for 'categoria' which we will handle later.
ALTER TABLE `users` 
  ADD COLUMN `nome` varchar(128) NOT NULL COMMENT 'Nome do usuário' AFTER `password`,
  ADD COLUMN `role` enum('admin','aluno','professor','supervisor') NOT NULL DEFAULT 'aluno' COMMENT 'roles' AFTER `nome`,
  CHANGE COLUMN `numero` `identificacao` int(9) DEFAULT NULL COMMENT 'Registro do aluno, SIAPE do professor ou CRESS do supervisor',
  ADD COLUMN `entidade_id` int(11) DEFAULT NULL COMMENT 'id da entidade: aluno, professor ou supervisor' AFTER `identificacao`,
  ADD COLUMN `ativo` tinyint(1) DEFAULT 1 AFTER `entidade_id`,
  ADD COLUMN `criado_em` timestamp NOT NULL DEFAULT current_timestamp() AFTER `ativo`,
  CHANGE COLUMN `timestamp` `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- Table USERS: Update the values of the new 'role' field based on the old 'categoria' field
UPDATE `users` SET `role` = 'admin' WHERE `categoria` = '1';
UPDATE `users` SET `role` = 'aluno' WHERE `categoria` = '2';
UPDATE `users` SET `role` = 'professor' WHERE `categoria` = '3';
UPDATE `users` SET `role` = 'supervisor' WHERE `categoria` = '4';

-- Table INSTITUICOES: Change the name of the estagio table to instituicoes
ALTER TABLE `estagio` RENAME TO `instituicoes`;
ALTER TABLE `instituicoes` RENAME COLUMN `area` TO `area_id`;
ALTER TABLE `instituicoes` DROP COLUMN `avaliacao`;
ALTER TABLE `instituicoes` DROP COLUMN `localInscricao`;
ALTER TABLE `instituicoes` DROP COLUMN `fax`;

-- Table CONFIGURACOES: Change the name of the configuracao table to configuracoes
ALTER TABLE `configuracao` RENAME TO `configuracoes`;

-- Table MURAL_ESTAGIOS: Change the name of the mural_estagio table to mural_estagios
ALTER TABLE `mural_estagio` RENAME TO `mural_estagios`;
ALTER TABLE `mural_estagios` RENAME COLUMN `dataSelecao` TO `data_selecao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `cargaHoraria` TO `carga_horaria`;
ALTER TABLE `mural_estagios` RENAME COLUMN `dataInscricao` TO `data_inscricao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `horarioSelecao` TO `horario_selecao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `localSelecao` TO `local_selecao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `formaSelecao` TO `forma_selecao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `localInscricao` TO `local_inscricao`;
ALTER TABLE `mural_estagios` RENAME COLUMN `id_estagio` TO `instituicao_id`;
ALTER TABLE `mural_estagios` DROP COLUMN `id_area`;
ALTER TABLE `mural_estagios` DROP COLUMN `datafax`;

-- Table TURNOS: Create table turnos
CREATE TABLE IF NOT EXISTS `turnos` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `turno` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Turnos de estagiários.';
INSERT INTO `turnos` (`turno`) VALUES ('diurno'), ('noturno'), ('integral'), ('outro');

-- Table ESTAGIARIOS: Alter table estagiarios
ALTER TABLE `estagiarios` RENAME COLUMN `alunonovo_id` TO `aluno_id`;
ALTER TABLE `estagiarios` RENAME COLUMN `id_instituicao` TO `instituicao_id`;
ALTER TABLE `estagiarios` RENAME COLUMN `id_supervisor` TO `supervisor_id`;
ALTER TABLE `estagiarios` RENAME COLUMN `id_professor` TO `professor_id`;
ALTER TABLE `estagiarios` DROP COLUMN `id_aluno`;
ALTER TABLE `estagiarios` DROP COLUMN `id_area`;
ALTER TABLE `estagiarios` DROP COLUMN `turno`;

-- Table TURMA_ESTAGIOS: Rename to turma_estagios
ALTER TABLE `areas_estagio` RENAME TO `turma_estagios`;

-- Table PROFESSORES: Alter table professores
ALTER TABLE `professores` ADD COLUMN `cress` varchar(10) NULL AFTER `siape`;
ALTER TABLE `professores` ADD COLUMN `regiao` varchar(2) NULL AFTER `cress`;
ALTER TABLE `professores` CHANGE COLUMN `cpf` varchar(15) NULL;
ALTER TABLE `professores` CHANGE COLUMN `telefone` varchar(15) NULL;
ALTER TABLE `professores` CHANGE COLUMN `celular` varchar(15) NULL;
ALTER TABLE `professores` DROP COLUMN `datanascimento`;
ALTER TABLE `professores` DROP COLUMN `localnascimento`;
ALTER TABLE `professores` DROP COLUMN `sexo`;
ALTER TABLE `professores` change COLUMN `ddd_telefone` `codigo_telefone` tinyint(2) NULL DEFAULT 21 AFTER `telefone`;
ALTER TABLE `professores` change COLUMN `ddd_celular` `codigo_celular` tinyint(2) NULL DEFAULT 21 AFTER `celular`;
ALTER TABLE `professores` DROP COLUMN `homepage`;
ALTER TABLE `professores` DROP COLUMN `redesocial`;
ALTER TABLE `professores` DROP COLUMN `curriculosigma`;
ALTER TABLE `professores` DROP COLUMN `pesquisadordgp`;
ALTER TABLE `professores` DROP COLUMN `formacaoprofissional`;
ALTER TABLE `professores` DROP COLUMN `universidadedegraduacao`;
ALTER TABLE `professores` DROP COLUMN `anoformacao`;
ALTER TABLE `professores` DROP COLUMN `mestradoarea`;
ALTER TABLE `professores` DROP COLUMN `mestradouniversidade`;
ALTER TABLE `professores` DROP COLUMN `mestradoanoconclusao`;
ALTER TABLE `professores` DROP COLUMN `doutoradoarea`;
ALTER TABLE `professores` DROP COLUMN `doutoradouniversidade`;
ALTER TABLE `professores` DROP COLUMN `doutoradoanoconclusao`;
ALTER TABLE `professores` DROP COLUMN `formaingresso`;
ALTER TABLE `professores` DROP COLUMN `tipocargo`;
ALTER TABLE `professores` DROP COLUMN `categoria`;
ALTER TABLE `professores` DROP COLUMN `regimetrabalho`;

UPDATE `professores` 
SET `telefone` = CONCAT('(', codigo_telefone, ') ', telefone);

UPDATE `professores` 
SET `celular` = CONCAT('(', codigo_celular, ') ', celular);

-- Table Questionários.
CREATE TABLE IF NOT EXISTS `questionarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'O título do questionário',
  `description` text NOT NULL COMMENT 'Uma descrição mais detalhada do questionário',
  `created` datetime NOT NULL COMMENT 'Timestamp quando o questionário foi criado',
  `modified` datetime NOT NULL COMMENT 'Timestamp quando o questionário foi modificado pela última vez',
  `is_active` tinyint(1) NOT NULL COMMENT 'Se o questionário está ativo e disponível para uso',
  `category` varchar(100) NOT NULL COMMENT 'Categoria opcional para agrupar questionários (por exemplo, "Feedback de Aluno", "Avaliação de Curso")',
  `target_user_type` varchar(50) NOT NULL COMMENT 'Tipo de usuário alvo para o questionário',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Questionários.';

-- Table Questões de avaliação. Substitui a tabela avaliacao.
CREATE TABLE IF NOT EXISTS `questoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL,
  `text` text NOT NULL COMMENT 'O texto da questão',
  `type` varchar(50) NOT NULL COMMENT 'O tipo de questão (text, textarea, select, scale, boolean)',
  `options` text NOT NULL COMMENT 'JSON encoded options for select/scale questions',
  `created` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp when the question was created',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Timestamp when the question was last modified',
  `ordem` int(11) NOT NULL COMMENT 'The order in which the question should appear in the questionnaire',
  PRIMARY KEY (`id`),
  KEY `questionnaire_id` (`questionario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Questões de avaliação.';

-- Table Respostas às perguntas de avaliação. Substitui a tabela avaliacao.
CREATE TABLE IF NOT EXISTS `respostas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL COMMENT 'The questionnaire id',
  `estagiario_id` int(11) NOT NULL COMMENT 'ID of the user who answered the question',
  `response` text NOT NULL COMMENT 'The answer to the question',
  `created` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp when the response was created',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Timestamp when the response was last modified',
  PRIMARY KEY (`id`),
  KEY `estagiarios_id` (`estagiario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Respostas às perguntas de avaliação. Substitui a tabela avaliacao.';

-- Table VISITAS: Alter table visita
ALTER TABLE `visita` RENAME `visitas`;
ALTER TABLE `visitas` CHANGE COLUMN `estagio_id` `instituicao_id` int(11) NOT NULL;

-- Change alunos table
ALTER TABLE `alunos` ADD COLUMN `turno_id` smallint(3) NOT NULL AFTER `turno`;
UPDATE `alunos` SET `turno_id` = (SELECT `id` FROM `turnos` WHERE `turno` = `alunos`.`turno`);

ALTER TABLE `alunos` CHANGE COLUMN `cpf`  varchar(15) NULL AFTER `registro`;
ALTER TABLE `alunos` CHANGE COLUMN `telefone`  varchar(15) NULL AFTER `codigo_telefone`;
ALTER TABLE `alunos` CHANGE COLUMN `celular`  varchar(15) NULL AFTER `codigo_celular`;

UPDATE `alunos` 
SET `telefone` = CONCAT('(', codigo_telefone, ') ', telefone);

UPDATE `alunos` 
SET `celular` = CONCAT('(', codigo_celular, ') ', celular);

-- Table SUPERVISORES: Change supervisores table
ALTER TABLE `supervisores` MODIFY COLUMN `cpf`  varchar(15) NULL;
ALTER TABLE `supervisores` MODIFY COLUMN `telefone`  varchar(15) NULL;
ALTER TABLE `supervisores` MODIFY COLUMN `celular`  varchar(15) NULL;
ALTER TABLE `supervisores` MODIFY COLUMN `escola`  varchar(70) NULL;
ALTER TABLE `supervisores` CHANGE COLUMN `codigo_cel` 'codigo_celular' varchar(2) NOT NULL DEFAULT 21;
ALTER TABLE `supervisores` CHANGE COLUMN `codigo_tel` 'codigo_telefone' varchar(2) NOT NULL DEFAULT 21;
ALTER TABLE `supervisores` CHANGE COLUMN `ano_formatura` 'ano_formacao' smallint(4) NULL;
ALTER TABLE `supervisores` DROP COLUMN `outros_estudos`;
ALTER TABLE `supervisores` DROP COLUMN `area_curso`;
ALTER TABLE `supervisores` DROP COLUMN `ano_curso`;
ALTER TABLE `supervisores` DROP COLUMN `num_inscricao`;
ALTER TABLE `supervisores` DROP COLUMN `curso_turma`;
ALTER TABLE `supervisores` DROP COLUMN `endereco`;
ALTER TABLE `supervisores` DROP COLUMN `bairro`;
ALTER TABLE `supervisores` DROP COLUMN `municipio`;
ALTER TABLE `supervisores` DROP COLUMN `cep`;

UPDATE `supervisores` 
SET `telefone` = CONCAT('(', codigo_telefone, ') ', telefone);

UPDATE `supervisores` 
SET `celular` = CONCAT('(', codigo_celular, ') ', celular);

-- Table INST_SUPER: Alter inst_super table
ALTER TABLE `inst_super` RENAME COLUMN `id_supervisor` TO `supervisor_id`;
ALTER TABLE `inst_super` RENAME COLUMN `id_instituicao` TO `instituicao_id`;

-- Table INSCRICOES: Alter mural_inscricao table
ALTER TABLE `mural_inscricao` RENAME TO `inscricoes`;
ALTER TABLE `inscricoes` RENAME COLUMN `id_aluno` TO `registro`;
ALTER TABLE `inscricoes` RENAME COLUMN `id_instituicao` TO `muralestagio_id`;
ALTER TABLE `inscricoes` DROP COLUMN `alunonovo_id`;
