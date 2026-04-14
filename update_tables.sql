-- Script to update tables structure and data from old ess_apps to new mural5_com_avaliacao.sql

-- Table USERS: Add new columns and change types of existing columns, except for 'categoria' which we will handle later.
ALTER TABLE IF EXISTS `users`
  ADD COLUMN `nome` varchar(128) NOT NULL COMMENT 'Nome do usuário' AFTER `password`,
  ADD COLUMN `role` enum('admin','aluno','professor','supervisor') NOT NULL DEFAULT 'aluno' COMMENT 'roles' AFTER `nome`,
  CHANGE COLUMN `numero` `identificacao` int(9) DEFAULT NULL COMMENT 'Registro do aluno, SIAPE do professor ou CRESS do supervisor',
  ADD COLUMN `entidade_id` int(11) DEFAULT NULL COMMENT 'id da entidade: aluno, professor ou supervisor' AFTER `identificacao`,
  ADD COLUMN `ativo` tinyint(1) DEFAULT 1 AFTER `entidade_id`,
  ADD COLUMN `criado_em` timestamp NOT NULL DEFAULT current_timestamp() AFTER `ativo`,
  CHANGE COLUMN `timestamp` `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- Table USERS: Update the values of the new 'role' field based on the old 'categoria' field
UPDATE `users` SET `role` = CASE `categoria` WHEN '1' THEN 'admin' WHEN '2' THEN 'aluno' WHEN '3' THEN 'professor' WHEN '4' THEN 'supervisor' ELSE `role` END WHERE `categoria` IN ('1','2','3','4');

-- Table INSTITUICOES: Change the name of the estagio table to instituicoes
ALTER TABLE IF EXISTS `estagio` RENAME TO `instituicoes`;

-- Drop columns only if they exist
ALTER TABLE `instituicoes`
    RENAME COLUMN IF EXISTS `area` TO `area_id`,
    DROP COLUMN IF EXISTS `avaliacao`,
    DROP COLUMN IF EXISTS `localInscricao`,
    DROP COLUMN IF EXISTS `fax`;

-- Table CONFIGURACOES: Change the name of the configuracao table to configuracoes
ALTER TABLE IF EXISTS `configuracao` RENAME TO `configuracoes`; 
ALTER TABLE `configuracoes` ADD COLUMN IF NOT EXISTS `instituicao` varchar(50) NOT NULL;

-- Table MURAL_ESTAGIOS: Change the name of the mural_estagio table to mural_estagios and rename columns
ALTER TABLE IF EXISTS `mural_estagio` RENAME TO `mural_estagios`;

ALTER TABLE `mural_estagios` 
    RENAME COLUMN IF EXISTS `dataSelecao` TO `data_selecao`,
    RENAME COLUMN IF EXISTS `cargaHoraria` TO `carga_horaria`,
    RENAME COLUMN IF EXISTS `dataInscricao` TO `data_inscricao`,
    RENAME COLUMN IF EXISTS `horarioSelecao` TO `horario_selecao`,
    RENAME COLUMN IF EXISTS `localSelecao` TO `local_selecao`,
    RENAME COLUMN IF EXISTS `formaSelecao` TO `forma_selecao`,
    RENAME COLUMN IF EXISTS `localInscricao` TO `local_inscricao`,
    RENAME COLUMN IF EXISTS `id_estagio` TO `instituicao_id`,
    DROP COLUMN IF EXISTS `id_area`,
    DROP COLUMN IF EXISTS `datafax`;

-- Table TURNOS: Create table turnos
CREATE TABLE IF NOT EXISTS `turnos` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `turno` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Turnos de estagiários.';

INSERT INTO `turnos` (`turno`) VALUES ('diurno'), ('noturno'), ('integral'), ('outro');

-- Table ESTAGIARIOS: Alter table estagiarios
ALTER TABLE `estagiarios` 
    RENAME COLUMN IF EXISTS `alunonovo_id` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`,
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_professor` TO `professor_id`,
    DROP COLUMN IF EXISTS `id_aluno`,
    DROP COLUMN IF EXISTS `id_area`,
    DROP COLUMN IF EXISTS `turno`;

-- Table TURMA_ESTAGIOS: Rename to turma_estagios
ALTER TABLE IF EXISTS `areas_estagio` RENAME TO `turma_estagios`;

-- Table PROFESSORES: Alter table professores
ALTER TABLE IF EXISTS `professores`
    ADD COLUMN IF NOT EXISTS `cress` varchar(10) NULL AFTER `siape`,
    ADD COLUMN IF NOT EXISTS `regiao` varchar(2) NULL AFTER `cress`,
    MODIFY COLUMN `cpf` varchar(15) NULL,
    MODIFY COLUMN `telefone` varchar(15) NULL,
    MODIFY COLUMN `celular` varchar(15) NULL,
    DROP COLUMN IF EXISTS `datanascimento`,
    DROP COLUMN IF EXISTS `localnascimento`,
    DROP COLUMN IF EXISTS `sexo`,
    CHANGE COLUMN `ddd_telefone` `codigo_telefone` tinyint(2) NULL DEFAULT 21 AFTER `telefone`,
    CHANGE COLUMN `ddd_celular` `codigo_celular` tinyint(2) NULL DEFAULT 21 AFTER `celular`,
    DROP COLUMN IF EXISTS `homepage`,
    DROP COLUMN IF EXISTS `redesocial`,
    DROP COLUMN IF EXISTS `curriculosigma`,
    DROP COLUMN IF EXISTS `pesquisadordgp`,
    DROP COLUMN IF EXISTS `formacaoprofissional`,
    DROP COLUMN IF EXISTS `universidadedegraduacao`,
    DROP COLUMN IF EXISTS `anoformacao`,
    DROP COLUMN IF EXISTS `mestradoarea`,
    DROP COLUMN IF EXISTS `mestradouniversidade`,
    DROP COLUMN IF EXISTS `mestradoanoconclusao`,
    DROP COLUMN IF EXISTS `doutoradoarea`,
    DROP COLUMN IF EXISTS `doutoradouniversidade`,
    DROP COLUMN IF EXISTS `doutoradoanoconclusao`,
    DROP COLUMN IF EXISTS `formaingresso`,
    DROP COLUMN IF EXISTS `tipocargo`,
    DROP COLUMN IF EXISTS `categoria`,
    DROP COLUMN IF EXISTS `regimetrabalho`;

-- Update professores phone numbers
UPDATE `professores` 
SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, ''))
WHERE codigo_telefone IS NOT NULL AND telefone IS NOT NULL;

UPDATE `professores` 
SET `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE codigo_celular IS NOT NULL AND celular IS NOT NULL;

-- Table Questionários
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

-- Table Questões de avaliação
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

-- Table Respostas às perguntas de avaliação
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
ALTER TABLE IF EXISTS `visita` RENAME TO `visitas`;
ALTER TABLE `visitas` CHANGE COLUMN `estagio_id` `instituicao_id` INT(11) NOT NULL;

-- Table ALUNOS: Alter alunos table
ALTER TABLE IF EXISTS `alunos` 
    ADD COLUMN `turno_id` SMALLINT(3) NOT NULL AFTER `turno`,
    MODIFY COLUMN `cpf` VARCHAR(15) NULL AFTER `registro`,
    MODIFY COLUMN `telefone` VARCHAR(15) NULL AFTER `codigo_telefone`,
    MODIFY COLUMN `celular` VARCHAR(15) NULL AFTER `codigo_celular`;

-- Update turno_id with proper join
UPDATE `alunos` a
INNER JOIN `turnos` t ON t.`turno` = a.`turno`
SET a.`turno_id` = t.`id`;

-- Update alunos phone numbers
UPDATE `alunos` 
SET 
    `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, '')),
    `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE 
    (codigo_telefone IS NOT NULL AND telefone IS NOT NULL)
    OR (codigo_celular IS NOT NULL AND celular IS NOT NULL);

-- Table SUPERVISORES: Alter supervisores table
ALTER TABLE IF EXISTS `supervisores` 
    MODIFY COLUMN IF EXISTS `cpf` VARCHAR(15) NULL,
    MODIFY COLUMN IF EXISTS `telefone` VARCHAR(15) NULL,
    MODIFY COLUMN IF EXISTS `celular` VARCHAR(15) NULL,
    MODIFY COLUMN IF EXISTS `escola` VARCHAR(70) NULL,
    CHANGE COLUMN IF EXISTS `codigo_cel` `codigo_celular` VARCHAR(2) NOT NULL DEFAULT '21',
    CHANGE COLUMN IF EXISTS `codigo_tel` `codigo_telefone` VARCHAR(2) NOT NULL DEFAULT '21',
    CHANGE COLUMN IF EXISTS `ano_formatura` `ano_formacao` SMALLINT(4) NULL,
    DROP COLUMN IF EXISTS `outros_estudos`,
    DROP COLUMN IF EXISTS `area_curso`,
    DROP COLUMN IF EXISTS `ano_curso`,
    DROP COLUMN IF EXISTS `num_inscricao`,
    DROP COLUMN IF EXISTS `curso_turma`,
    DROP COLUMN IF EXISTS `endereco`,
    DROP COLUMN IF EXISTS `bairro`,
    DROP COLUMN IF EXISTS `municipio`,
    DROP COLUMN IF EXISTS `cep`;

-- Update supervisores phone numbers
UPDATE `supervisores` 
SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, ''))
WHERE codigo_telefone IS NOT NULL 
  AND telefone IS NOT NULL 
  AND telefone NOT LIKE CONCAT('(', codigo_telefone, ')%');

UPDATE `supervisores` 
SET `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE codigo_celular IS NOT NULL 
  AND celular IS NOT NULL 
  AND celular NOT LIKE CONCAT('(', codigo_celular, ')%');

-- Table INST_SUPER: Alter inst_super 
ALTER TABLE `inst_super` 
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`;

-- Table INSCRICOES: Alter mural_inscricao table
ALTER TABLE IF EXISTS `mural_inscricao` RENAME TO `inscricoes`;

ALTER TABLE `inscricoes` 
    RENAME COLUMN IF EXISTS `id_aluno` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `muralestagio_id`,
    DROP COLUMN IF EXISTS `alunonovo_id`;

-- Verify the changes
SELECT COLUMN_NAME, DATA_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_NAME = 'inscricoes'
ORDER BY ORDINAL_POSITION;

-- Table AREA_INSTITUICOES: Alter area_instituicoes table
ALTER TABLE IF EXISTS `area_instituicoes` RENAME TO `areas`;
