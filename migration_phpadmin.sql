-- =====================================================
-- MIGRATION SCRIPT: ess_apps to mural5_com_avaliacao
-- VERSÃO CORRIGIDA - COMPATÍVEL COM phpMyAdmin
-- =====================================================

START TRANSACTION;

-- =====================================================
-- PART 1: PRE-MIGRATION VALIDATION CHECKS
-- =====================================================

-- Create temporary table to store validation results
CREATE TEMPORARY TABLE IF NOT EXISTS validation_errors (
    error_id INT AUTO_INCREMENT PRIMARY KEY,
    error_type VARCHAR(100),
    error_message TEXT,
    severity VARCHAR(20)
);

-- Check if required columns exist before modifying
INSERT INTO validation_errors (error_type, error_message, severity)
SELECT 'MISSING_COLUMN', 
       CONCAT('Column `numero` does not exist in `users` table'),
       'WARNING'
WHERE NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS 
    WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'numero'
);

INSERT INTO validation_errors (error_type, error_message, severity)
SELECT 'MISSING_COLUMN', 
       CONCAT('Column `timestamp` does not exist in `users` table'),
       'WARNING'
WHERE NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS 
    WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'timestamp'
);

-- Display validation results
SELECT '========================================' AS '';
SELECT 'VALIDATION RESULTS' AS '';
SELECT '========================================' AS '';
SELECT * FROM validation_errors;

-- =====================================================
-- PART 2: MAIN MIGRATION SCRIPT
-- =====================================================

-- Table USERS - Check what columns exist first
SET @has_numero = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'numero');
SET @has_timestamp = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'timestamp');

-- Alter users table based on existing columns
ALTER TABLE IF EXISTS `users`
  ADD COLUMN IF NOT EXISTS `nome` varchar(128) NOT NULL COMMENT 'Nome do usuário' AFTER `password`,
  ADD COLUMN IF NOT EXISTS `role` enum('admin','aluno','professor','supervisor') NOT NULL DEFAULT 'aluno' COMMENT 'roles' AFTER `nome`,
  ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  ADD COLUMN IF NOT EXISTS `entidade_id` int(11) DEFAULT NULL COMMENT 'id da entidade';

-- Handle 'numero' column if it exists
IF @has_numero > 0 THEN
    ALTER TABLE `users` CHANGE COLUMN `numero` `identificacao` int(9) DEFAULT NULL COMMENT 'Registro do aluno, SIAPE do professor ou CRESS do supervisor';
ELSE
    ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `identificacao` int(9) DEFAULT NULL COMMENT 'Registro do aluno, SIAPE do professor ou CRESS do supervisor';
END IF;

-- Handle 'timestamp' column if it exists
IF @has_timestamp > 0 THEN
    ALTER TABLE `users` CHANGE COLUMN `timestamp` `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ELSE
    ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
END IF;

-- Update user roles (check if categoria column exists first)
SET @has_categoria = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'categoria');

IF @has_categoria > 0 THEN
    UPDATE `users` SET `role` = CASE `categoria` 
        WHEN '1' THEN 'admin' 
        WHEN '2' THEN 'aluno' 
        WHEN '3' THEN 'professor' 
        WHEN '4' THEN 'supervisor' 
        ELSE `role` 
    END WHERE `categoria` IN ('1','2','3','4');
END IF;

-- Table INSTITUICOES
ALTER TABLE IF EXISTS `estagio` RENAME TO `instituicoes`;
ALTER TABLE `instituicoes`
    RENAME COLUMN IF EXISTS `area` TO `area_id`,
    DROP COLUMN IF EXISTS `avaliacao`,
    DROP COLUMN IF EXISTS `localInscricao`,
    DROP COLUMN IF EXISTS `fax`;

-- Table CONFIGURACOES
ALTER TABLE IF EXISTS `configuracao` RENAME TO `configuracoes`;
ALTER TABLE `configuracoes` ADD COLUMN IF NOT EXISTS `instituicao` varchar(50) NOT NULL;

-- Table MURAL_ESTAGIOS
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

-- Table TURNOS
CREATE TABLE IF NOT EXISTS `turnos` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `turno` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `turnos` (`turno`) VALUES ('diurno'), ('noturno'), ('integral'), ('outro');

-- Table ESTAGIARIOS
ALTER TABLE `estagiarios` 
    RENAME COLUMN IF EXISTS `alunonovo_id` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`,
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_professor` TO `professor_id`,
    DROP COLUMN IF EXISTS `id_aluno`,
    DROP COLUMN IF EXISTS `id_area`,
    DROP COLUMN IF EXISTS `turno`;

-- Table TURMA_ESTAGIOS
ALTER TABLE IF EXISTS `areas_estagio` RENAME TO `turma_estagios`;

-- Table PROFESSORES
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

-- Update professores phone numbers (check if columns exist first)
SET @has_codigo_telefone = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'professores' AND COLUMN_NAME = 'codigo_telefone');
SET @has_telefone = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'professores' AND COLUMN_NAME = 'telefone');

IF @has_codigo_telefone > 0 AND @has_telefone > 0 THEN
    UPDATE `professores` 
    SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, ''))
    WHERE codigo_telefone IS NOT NULL AND telefone IS NOT NULL;
END IF;

SET @has_codigo_celular = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'professores' AND COLUMN_NAME = 'codigo_celular');
SET @has_celular = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'professores' AND COLUMN_NAME = 'celular');

IF @has_codigo_celular > 0 AND @has_celular > 0 THEN
    UPDATE `professores` 
    SET `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
    WHERE codigo_celular IS NOT NULL AND celular IS NOT NULL;
END IF;

-- Table Questionários
CREATE TABLE IF NOT EXISTS `questionarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `category` varchar(100) NOT NULL,
  `target_user_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Questões
CREATE TABLE IF NOT EXISTS `questoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `options` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ordem` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_id` (`questionario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Respostas
CREATE TABLE IF NOT EXISTS `respostas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL,
  `estagiario_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `estagiarios_id` (`estagiario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table VISITAS
ALTER TABLE IF EXISTS `visita` RENAME TO `visitas`;
ALTER TABLE `visitas` CHANGE COLUMN `estagio_id` `instituicao_id` INT(11) NOT NULL;

-- Table ALUNOS
ALTER TABLE IF EXISTS `alunos` 
    ADD COLUMN IF NOT EXISTS `turno_id` SMALLINT(3) NOT NULL AFTER `turno`,
    MODIFY COLUMN `cpf` VARCHAR(15) NULL,
    MODIFY COLUMN `telefone` VARCHAR(15) NULL,
    MODIFY COLUMN `celular` VARCHAR(15) NULL;

-- Reposition columns after modifications (check if columns exist)
ALTER TABLE `alunos`
    MODIFY COLUMN IF EXISTS `cpf` VARCHAR(15) NULL AFTER `registro`,
    MODIFY COLUMN IF EXISTS `telefone` VARCHAR(15) NULL AFTER `codigo_telefone`,
    MODIFY COLUMN IF EXISTS `celular` VARCHAR(15) NULL AFTER `codigo_celular`;

-- Update turno_id
UPDATE `alunos` a
INNER JOIN `turnos` t ON t.`turno` = a.`turno`
SET a.`turno_id` = t.`id`;

-- Update alunos phone numbers
UPDATE `alunos` 
SET 
    `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, '')),
    `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE (codigo_telefone IS NOT NULL AND telefone IS NOT NULL)
   OR (codigo_celular IS NOT NULL AND celular IS NOT NULL);

-- Table SUPERVISORES
ALTER TABLE IF EXISTS `supervisores` 
    MODIFY COLUMN `cpf` VARCHAR(15) NULL,
    MODIFY COLUMN `telefone` VARCHAR(15) NULL,
    MODIFY COLUMN `celular` VARCHAR(15) NULL,
    MODIFY COLUMN `escola` VARCHAR(70) NULL,
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

-- Table INST_SUPER
ALTER TABLE `inst_super` 
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`;

-- Table INSCRICOES
ALTER TABLE IF EXISTS `mural_inscricao` RENAME TO `inscricoes`;
ALTER TABLE `inscricoes` 
    RENAME COLUMN IF EXISTS `id_aluno` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `muralestagio_id`,
    DROP COLUMN IF EXISTS `alunonovo_id`;

-- Table AREA_INSTITUICOES
ALTER TABLE IF EXISTS `area_instituicoes` RENAME TO `areas`;

-- =====================================================
-- PART 3: POST-MIGRATION SUMMARY
-- =====================================================

SELECT '========================================' AS '';
SELECT '✅ MIGRATION COMPLETED SUCCESSFULLY' AS '';
SELECT '========================================' AS '';

-- Show final table count
SELECT COUNT(*) AS total_tables_after_migration
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE();

-- Show critical tables
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME IN ('users', 'instituicoes', 'mural_estagios', 'turnos', 
                     'estagiarios', 'professores', 'supervisores', 'alunos', 
                     'inscricoes', 'areas', 'questionarios', 'questoes', 'respostas')
ORDER BY TABLE_NAME;

-- =====================================================
-- PART 4: CLEAN UP
-- =====================================================

DROP TEMPORARY TABLE IF EXISTS validation_errors;

-- Commit all changes
COMMIT;

SELECT 'All changes committed successfully!' AS '';